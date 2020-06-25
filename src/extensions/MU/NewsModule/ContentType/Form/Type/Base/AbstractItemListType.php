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

declare(strict_types=1);

namespace MU\NewsModule\ContentType\Form\Type\Base;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Translation\Extractor\Annotation\Ignore;
use Translation\Extractor\Annotation\Translate;
use Zikula\CategoriesModule\Entity\RepositoryInterface\CategoryRepositoryInterface;
use Zikula\CategoriesModule\Form\Type\CategoriesType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;
use Zikula\ExtensionsModule\ModuleInterface\Content\Form\Type\AbstractContentFormType;
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
        CategoryRepositoryInterface $categoryRepository
    ) {
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

    /**
     * Adds an object type field.
     */
    public function addObjectTypeField(FormBuilderInterface $builder, array $options = []): void
    {
        $helpText = /** @Translate */'If you change this please save the element once to reload the parameters below.';
        $builder->add('objectType', ChoiceType::class, [
            'label' => 'Object type:',
            'empty_data' => 'message',
            'attr' => [
                /** @Ignore */
                'title' => $helpText
            ],
            /** @Ignore */
            'help' => $helpText,
            'choices' => [
                'Messages' => 'message',
                'Images' => 'image'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a categories field.
     */
    public function addCategoriesField(FormBuilderInterface $builder, array $options = []): void
    {
        if (!$options['is_categorisable'] || null === $options['category_helper']) {
            return;
        }
    
        $objectType = $options['object_type'];
        $label = $hasMultiSelection
            ? /** @Translate */'Categories'
            : /** @Translate */'Category'
        ;
        $hasMultiSelection = $options['category_helper']->hasMultipleSelection($objectType);
        $entityCategoryClass = 'MU\NewsModule\Entity\\' . ucfirst($objectType) . 'CategoryEntity';
        $builder->add('categories', CategoriesType::class, [
            /** @Ignore */
            'label' => $label . ':',
            'empty_data' => $hasMultiSelection ? [] : null,
            'attr' => [
                'class' => 'category-selector',
                'title' => 'This is an optional filter.'
            ],
            'help' => 'This is an optional filter.',
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
    public function addSortingField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('sorting', ChoiceType::class, [
            'label' => 'Sorting:',
            'label_attr' => [
                'class' => 'radio-custom'
            ],
            'empty_data' => 'default',
            'choices' => [
                'Random' => 'random',
                'Newest' => 'newest',
                'Updated' => 'updated',
                'Default' => 'default'
            ],
            'multiple' => false,
            'expanded' => true
        ]);
    }

    /**
     * Adds a page size field.
     */
    public function addAmountField(FormBuilderInterface $builder, array $options = []): void
    {
        $helpText = /** @Translate */'The maximum amount of items to be shown.'
            . ' ' . /** @Translate */'Only digits are allowed.'
        ;
        $builder->add('amount', IntegerType::class, [
            'label' => 'Amount:',
            'attr' => [
                'maxlength' => 2,
                /** @Ignore */
                'title' => $helpText
            ],
            /** @Ignore */
            'help' => $helpText,
            'empty_data' => 5
        ]);
    }

    /**
     * Adds template fields.
     */
    public function addTemplateFields(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('template', ChoiceType::class, [
            'label' => 'Template:',
            'empty_data' => 'itemlist_display.html.twig',
            'choices' => [
                'Only item titles' => 'itemlist_display.html.twig',
                'With description' => 'itemlist_display_description.html.twig',
                'Custom template' => 'custom'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
        $exampleTemplate = 'itemlist_[objectType]_display.html.twig';
        $builder->add('customTemplate', TextType::class, [
            'label' => 'Custom template:',
            'required' => false,
            'attr' => [
                'maxlength' => 80,
                /** @Ignore */
                'title' => /** @Translate */'Example' . ': ' . $exampleTemplate
            ],
            /** @Ignore */
            'help' => /** @Translate */'Example' . ': <code>' . $exampleTemplate . '</code>',
            'help_html' => true
        ]);
    }

    /**
     * Adds a filter field.
     */
    public function addFilterField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('filter', TextType::class, [
            'label' => 'Filter (expert option):',
            'required' => false,
            'attr' => [
                'maxlength' => 255,
                /** @Ignore */
                'title' => /** @Translate */'Example' . ': tbl.age >= 18'
            ],
            /** @Ignore */
            'help' => /** @Translate */'Example' . ': tbl.age >= 18'
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