<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Form\Type\QuickNavigation\Base;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\Bundle\FormExtensionBundle\Form\Type\LocaleType;
use Zikula\CategoriesModule\Form\Type\CategoriesType;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\SettingsModule\Api\ApiInterface\LocaleApiInterface;
use MU\NewsModule\Helper\FeatureActivationHelper;
use MU\NewsModule\Helper\ListEntriesHelper;

/**
 * Message quick navigation form type base class.
 */
abstract class AbstractMessageQuickNavType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * @var LocaleApiInterface
     */
    protected $localeApi;

    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;

    /**
     * MessageQuickNavType constructor.
     *
     * @param TranslatorInterface $translator   Translator service instance
     * @param ListEntriesHelper   $listHelper   ListEntriesHelper service instance
     * @param LocaleApiInterface  $localeApi    LocaleApi service instance
     * @param FeatureActivationHelper $featureActivationHelper FeatureActivationHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        ListEntriesHelper $listHelper,
        LocaleApiInterface $localeApi,
        FeatureActivationHelper $featureActivationHelper
    ) {
        $this->setTranslator($translator);
        $this->listHelper = $listHelper;
        $this->localeApi = $localeApi;
        $this->featureActivationHelper = $featureActivationHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(/*TranslatorInterface */$translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('all', HiddenType::class)
            ->add('own', HiddenType::class)
            ->add('tpl', HiddenType::class)
        ;

        if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, 'message')) {
            $this->addCategoriesField($builder, $options);
        }
        $this->addListFields($builder, $options);
        $this->addUserFields($builder, $options);
        $this->addLocaleFields($builder, $options);
        $this->addSearchField($builder, $options);
        $this->addSortingFields($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addBooleanFields($builder, $options);
        $builder->add('updateview', SubmitType::class, [
            'label' => $this->__('OK'),
            'attr' => [
                'class' => 'btn btn-default btn-sm'
            ]
        ]);
    }

    /**
     * Adds a categories field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addCategoriesField(FormBuilderInterface $builder, array $options)
    {
        $objectType = 'message';
    
        $builder->add('categories', CategoriesType::class, [
            'label' => $this->__('Categories'),
            'empty_data' => [],
            'attr' => [
                'class' => 'input-sm category-selector',
                'title' => $this->__('This is an optional filter.')
            ],
            'help' => $this->__('This is an optional filter.'),
            'required' => false,
            'multiple' => true,
            'module' => 'MUNewsModule',
            'entity' => ucfirst($objectType) . 'Entity',
            'entityCategoryClass' => 'MU\NewsModule\Entity\\' . ucfirst($objectType) . 'CategoryEntity'
        ]);
    }

    /**
     * Adds list fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addListFields(FormBuilderInterface $builder, array $options)
    {
        $listEntries = $this->listHelper->getEntries('message', 'workflowState');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('workflowState', ChoiceType::class, [
            'label' => $this->__('State'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => $choices,
            'choices_as_values' => true,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds user fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addUserFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('approver', EntityType::class, [
            'label' => $this->__('Approver'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            // Zikula core should provide a form type for this to hide entity details
            'class' => 'Zikula\UsersModule\Entity\UserEntity',
            'choice_label' => 'uname'
        ]);
    }

    /**
     * Adds locale fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addLocaleFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('messageLanguage', LocaleType::class, [
            'label' => $this->__('Message language'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'choices' => $this->localeApi->getSupportedLocaleNames(),
            'choices_as_values' => true
        ]);
    }

    /**
     * Adds a search field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSearchField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('q', SearchType::class, [
            'label' => $this->__('Search'),
            'attr' => [
                'maxlength' => 255,
                'class' => 'input-sm'
            ],
            'required' => false
        ]);
    }


    /**
     * Adds sorting fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSortingFields(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', ChoiceType::class, [
                'label' => $this->__('Sort by'),
                'attr' => [
                    'class' => 'input-sm'
                ],
                'choices' =>             [
                    $this->__('Workflow state') => 'workflowState',
                    $this->__('Title') => 'title',
                    $this->__('Start text') => 'startText',
                    $this->__('Image upload 1') => 'imageUpload1',
                    $this->__('Main text') => 'mainText',
                    $this->__('Author') => 'author',
                    $this->__('Notes') => 'notes',
                    $this->__('Display on index') => 'displayOnIndex',
                    $this->__('Message language') => 'messageLanguage',
                    $this->__('Allow comments') => 'allowComments',
                    $this->__('Image upload 2') => 'imageUpload2',
                    $this->__('Image upload 3') => 'imageUpload3',
                    $this->__('Image upload 4') => 'imageUpload4',
                    $this->__('No end date') => 'noEndDate',
                    $this->__('Creation date') => 'createdDate',
                    $this->__('Creator') => 'createdBy',
                    $this->__('Update date') => 'updatedDate',
                    $this->__('Updater') => 'updatedBy'
                ],
                'choices_as_values' => true,
                'required' => true,
                'expanded' => false
            ])
            ->add('sortdir', ChoiceType::class, [
                'label' => $this->__('Sort direction'),
                'empty_data' => 'asc',
                'attr' => [
                    'class' => 'input-sm'
                ],
                'choices' => [
                    $this->__('Ascending') => 'asc',
                    $this->__('Descending') => 'desc'
                ],
                'choices_as_values' => true,
                'required' => true,
                'expanded' => false
            ])
        ;
    }

    /**
     * Adds a page size field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addAmountField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('num', ChoiceType::class, [
            'label' => $this->__('Page size'),
            'empty_data' => 20,
            'attr' => [
                'class' => 'input-sm text-right'
            ],
            'choices' => [
                $this->__('5') => 5,
                $this->__('10') => 10,
                $this->__('15') => 15,
                $this->__('20') => 20,
                $this->__('30') => 30,
                $this->__('50') => 50,
                $this->__('100') => 100
            ],
            'choices_as_values' => true,
            'required' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds boolean fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addBooleanFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('displayOnIndex', ChoiceType::class, [
            'label' => $this->__('Display on index'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => [
                $this->__('No') => 'no',
                $this->__('Yes') => 'yes'
            ],
            'choices_as_values' => true
        ]);
        $builder->add('allowComments', ChoiceType::class, [
            'label' => $this->__('Allow comments'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => [
                $this->__('No') => 'no',
                $this->__('Yes') => 'yes'
            ],
            'choices_as_values' => true
        ]);
        $builder->add('noEndDate', ChoiceType::class, [
            'label' => $this->__('No end date'),
            'attr' => [
                'class' => 'input-sm'
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => [
                $this->__('No') => 'no',
                $this->__('Yes') => 'yes'
            ],
            'choices_as_values' => true
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'munewsmodule_messagequicknav';
    }
}
