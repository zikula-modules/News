<?php

/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @see https://homepages-mit-zikula.de
 * @see https://ziku.la
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\ContentType\Form\Type\Base;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\CategoriesModule\Entity\RepositoryInterface\CategoryRepositoryInterface;
use Zikula\CategoriesModule\Form\Type\CategoriesType;
use Zikula\Common\Content\AbstractContentFormType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\Common\Translator\TranslatorInterface;
use MU\NewsModule\Helper\FeatureActivationHelper;

/**
 * List content type form type base class.
 */
abstract class AbstractItemListType extends AbstractContentFormType
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    public function __construct(
        TranslatorInterface $translator,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->setTranslator($translator);
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addObjectTypeField($builder, $options);
        if (
            $options['feature_activation_helper']->isEnabled(
                FeatureActivationHelper::CATEGORIES,
                $options['object_type']
            )
        ) {
            $this->addCategoriesField($builder, $options);
        }
        $this->addSortingField($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addTemplateFields($builder, $options);
        $this->addFilterField($builder, $options);
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Adds an object type field.
     */
    public function addObjectTypeField(FormBuilderInterface $builder, array $options = [])
    {
        $helpText = $this->__(
            'If you change this please save the element once to reload the parameters below.',
            'munewsmodule'
        );
        $builder->add('objectType', ChoiceType::class, [
            'label' => $this->__('Object type:', 'munewsmodule'),
            'empty_data' => 'message',
            'attr' => [
                'title' => $helpText
            ],
            'help' => $helpText,
            'choices' => [
                $this->__('Messages', 'munewsmodule') => 'message',
                $this->__('Images', 'munewsmodule') => 'image'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a categories field.
     */
    public function addCategoriesField(FormBuilderInterface $builder, array $options = [])
    {
        if (!$options['is_categorisable'] || null === $options['category_helper']) {
            return;
        }
    
        $objectType = $options['object_type'];
        $label = $hasMultiSelection
            ? $this->__('Categories', 'munewsmodule')
            : $this->__('Category', 'munewsmodule')
        ;
        $hasMultiSelection = $options['category_helper']->hasMultipleSelection($objectType);
        $entityCategoryClass = 'MU\NewsModule\Entity\\' . ucfirst($objectType) . 'CategoryEntity';
        $builder->add('categories', CategoriesType::class, [
            'label' => $label . ':',
            'empty_data' => $hasMultiSelection ? [] : null,
            'attr' => [
                'class' => 'category-selector',
                'title' => $this->__('This is an optional filter.', 'munewsmodule')
            ],
            'help' => $this->__('This is an optional filter.', 'munewsmodule'),
            'required' => false,
            'multiple' => $hasMultiSelection,
            'module' => 'MUNewsModule',
            'entity' => ucfirst($objectType) . 'Entity',
            'entityCategoryClass' => $entityCategoryClass,
            'showRegistryLabels' => true
        ]);
    
        $categoryRepository = $this->categoryRepository;
        $builder->get('categories')->addModelTransformer(new CallbackTransformer(
            function ($catIds) use ($categoryRepository, $objectType, $hasMultiSelection) {
                $categoryMappings = [];
                $entityCategoryClass = 'MU\NewsModule\Entity\\' . ucfirst($objectType) . 'CategoryEntity';
    
                $catIds = is_array($catIds) ? $catIds : explode(',', $catIds);
                foreach ($catIds as $catId) {
                    $category = $categoryRepository->find($catId);
                    if (null === $category) {
                        continue;
                    }
                    $mapping = new $entityCategoryClass(null, $category, null);
                    $categoryMappings[] = $mapping;
                }
    
                if (!$hasMultiSelection) {
                    $categoryMappings = 0 < count($categoryMappings) ? reset($categoryMappings) : null;
                }
    
                return $categoryMappings;
            },
            function ($result) use ($hasMultiSelection) {
                $catIds = [];
    
                foreach ($result as $categoryMapping) {
                    $catIds[] = $categoryMapping->getCategory()->getId();
                }
    
                return $catIds;
            }
        ));
    }

    /**
     * Adds a sorting field.
     */
    public function addSortingField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('sorting', ChoiceType::class, [
            'label' => $this->__('Sorting:', 'munewsmodule'),
            'label_attr' => [
                'class' => 'radio-inline'
            ],
            'empty_data' => 'default',
            'choices' => [
                $this->__('Random', 'munewsmodule') => 'random',
                $this->__('Newest', 'munewsmodule') => 'newest',
                $this->__('Updated', 'munewsmodule') => 'updated',
                $this->__('Default', 'munewsmodule') => 'default'
            ],
            'multiple' => false,
            'expanded' => true
        ]);
    }

    /**
     * Adds a page size field.
     */
    public function addAmountField(FormBuilderInterface $builder, array $options = [])
    {
        $helpText = $this->__('The maximum amount of items to be shown.', 'munewsmodule')
            . ' ' . $this->__('Only digits are allowed.', 'munewsmodule')
        ;
        $builder->add('amount', IntegerType::class, [
            'label' => $this->__('Amount:', 'munewsmodule'),
            'attr' => [
                'maxlength' => 2,
                'title' => $helpText
            ],
            'help' => $helpText,
            'empty_data' => 5
        ]);
    }

    /**
     * Adds template fields.
     */
    public function addTemplateFields(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('template', ChoiceType::class, [
            'label' => $this->__('Template:', 'munewsmodule'),
            'empty_data' => 'itemlist_display.html.twig',
            'choices' => [
                $this->__('Only item titles', 'munewsmodule') => 'itemlist_display.html.twig',
                $this->__('With description', 'munewsmodule') => 'itemlist_display_description.html.twig',
                $this->__('Custom template', 'munewsmodule') => 'custom'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
        $exampleTemplate = 'itemlist_[objectType]_display.html.twig';
        $builder->add('customTemplate', TextType::class, [
            'label' => $this->__('Custom template:', 'munewsmodule'),
            'required' => false,
            'attr' => [
                'maxlength' => 80,
                'title' => $this->__('Example', 'munewsmodule') . ': ' . $exampleTemplate
            ],
            'help' => $this->__('Example', 'munewsmodule') . ': <code>' . $exampleTemplate . '</code>'
        ]);
    }

    /**
     * Adds a filter field.
     */
    public function addFilterField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('filter', TextType::class, [
            'label' => $this->__('Filter (expert option):', 'munewsmodule'),
            'required' => false,
            'attr' => [
                'maxlength' => 255,
                'title' => $this->__('Example', 'munewsmodule') . ': tbl.age >= 18'
            ],
            'help' => $this->__('Example', 'munewsmodule') . ': tbl.age >= 18'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'munewsmodule_contenttype_list';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'context' => ContentTypeInterface::CONTEXT_EDIT,
                'object_type' => 'message',
                'is_categorisable' => false,
                'category_helper' => null,
                'feature_activation_helper' => null
            ])
            ->setRequired(['object_type'])
            ->setDefined(['is_categorisable', 'category_helper', 'feature_activation_helper'])
            ->setAllowedTypes('context', 'string')
            ->setAllowedTypes('object_type', 'string')
            ->setAllowedTypes('is_categorisable', 'bool')
            ->setAllowedTypes('category_helper', 'object')
            ->setAllowedTypes('feature_activation_helper', 'object')
            ->setAllowedValues('context', [ContentTypeInterface::CONTEXT_EDIT, ContentTypeInterface::CONTEXT_TRANSLATION])
        ;
    }
}
