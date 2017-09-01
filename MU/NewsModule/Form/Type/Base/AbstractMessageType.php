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

namespace MU\NewsModule\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Bundle\FormExtensionBundle\Form\Type\LocaleType;
use Zikula\CategoriesModule\Form\Type\CategoriesType;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\SettingsModule\Api\ApiInterface\LocaleApiInterface;
use MU\NewsModule\Entity\Factory\EntityFactory;
use MU\NewsModule\Form\Type\Field\TranslationType;
use MU\NewsModule\Form\Type\Field\UploadType;
use Zikula\UsersModule\Form\Type\UserLiveSearchType;
use MU\NewsModule\Helper\FeatureActivationHelper;
use MU\NewsModule\Helper\ListEntriesHelper;
use MU\NewsModule\Helper\TranslatableHelper;

/**
 * Message editing form type base class.
 */
abstract class AbstractMessageType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var TranslatableHelper
     */
    protected $translatableHelper;

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
     * MessageType constructor.
     *
     * @param TranslatorInterface $translator     Translator service instance
     * @param EntityFactory $entityFactory EntityFactory service instance
     * @param VariableApiInterface $variableApi VariableApi service instance
     * @param TranslatableHelper $translatableHelper TranslatableHelper service instance
     * @param ListEntriesHelper $listHelper ListEntriesHelper service instance
     * @param LocaleApiInterface $localeApi LocaleApi service instance
     * @param FeatureActivationHelper $featureActivationHelper FeatureActivationHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        VariableApiInterface $variableApi,
        TranslatableHelper $translatableHelper,
        ListEntriesHelper $listHelper,
        LocaleApiInterface $localeApi,
        FeatureActivationHelper $featureActivationHelper
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->variableApi = $variableApi;
        $this->translatableHelper = $translatableHelper;
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
        $this->addEntityFields($builder, $options);
        if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::ATTRIBUTES, 'message')) {
            $this->addAttributeFields($builder, $options);
        }
        if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, 'message')) {
            $this->addCategoriesField($builder, $options);
        }
        $this->addAdditionalNotificationRemarksField($builder, $options);
        $this->addModerationFields($builder, $options);
        $this->addReturnControlField($builder, $options);
        $this->addSubmitButtons($builder, $options);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            foreach (['imageUpload1', 'imageUpload2', 'imageUpload3', 'imageUpload4'] as $uploadFieldName) {
                $entity[$uploadFieldName] = [
                    $uploadFieldName => $entity[$uploadFieldName] instanceof File ? $entity[$uploadFieldName]->getPathname() : null
                ];
            }
        });
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $entity = $event->getData();
            foreach (['imageUpload1', 'imageUpload2', 'imageUpload3', 'imageUpload4'] as $uploadFieldName) {
                if (is_array($entity[$uploadFieldName])) {
                    $entity[$uploadFieldName] = $entity[$uploadFieldName][$uploadFieldName];
                }
            }
        });
    }

    /**
     * Adds basic entity fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addEntityFields(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('title', TextType::class, [
            'label' => $this->__('Title') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the title of the message')
            ],
            'required' => true,
        ]);
        
        $builder->add('startText', TextareaType::class, [
            'label' => $this->__('Start text') . ':',
            'help' => $this->__f('Note: this value must not exceed %amount% characters.', ['%amount%' => 10000]),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 10000,
                'class' => '',
                'title' => $this->__('Enter the start text of the message')
            ],
            'required' => true,
        ]);
        
        $builder->add('mainText', TextareaType::class, [
            'label' => $this->__('Main text') . ':',
            'help' => $this->__f('Note: this value must not exceed %amount% characters.', ['%amount%' => 20000]),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 20000,
                'class' => '',
                'title' => $this->__('Enter the main text of the message')
            ],
            'required' => false,
        ]);
        
        if ($this->variableApi->getSystemVar('multilingual') && $this->featureActivationHelper->isEnabled(FeatureActivationHelper::TRANSLATIONS, 'message')) {
            $supportedLanguages = $this->translatableHelper->getSupportedLanguages('message');
            if (is_array($supportedLanguages) && count($supportedLanguages) > 1) {
                $currentLanguage = $this->translatableHelper->getCurrentLanguage();
                $translatableFields = $this->translatableHelper->getTranslatableFields('message');
                $mandatoryFields = $this->translatableHelper->getMandatoryFields('message');
                foreach ($supportedLanguages as $language) {
                    if ($language == $currentLanguage) {
                        continue;
                    }
                    $builder->add('translations' . $language, TranslationType::class, [
                        'fields' => $translatableFields,
                        'mandatory_fields' => $mandatoryFields[$language],
                        'values' => isset($options['translations'][$language]) ? $options['translations'][$language] : []
                    ]);
                }
            }
        }
        
        $builder->add('imageUpload1', UploadType::class, [
            'label' => $this->__('Image upload 1') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the image upload 1 of the message')
            ],
            'required' => false && $options['mode'] == 'create',
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => '200k'
        ]);
        
        $builder->add('amountOfViews', IntegerType::class, [
            'label' => $this->__('Amount of views') . ':',
            'empty_data' => '0',
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the amount of views of the message.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => false,
            'scale' => 0
        ]);
        
        $builder->add('author', TextType::class, [
            'label' => $this->__('Author') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 100,
                'class' => '',
                'title' => $this->__('Enter the author of the message')
            ],
            'required' => true,
        ]);
        
        $builder->add('approver', UserLiveSearchType::class, [
            'label' => $this->__('Approver') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the approver of the message')
            ],
            'required' => false,
        ]);
        
        $builder->add('notes', TextareaType::class, [
            'label' => $this->__('Notes') . ':',
            'help' => $this->__f('Note: this value must not exceed %amount% characters.', ['%amount%' => 2000]),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 2000,
                'class' => '',
                'title' => $this->__('Enter the notes of the message')
            ],
            'required' => false,
        ]);
        
        $builder->add('displayOnIndex', CheckboxType::class, [
            'label' => $this->__('Display on index') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('display on index ?')
            ],
            'required' => false,
        ]);
        
        $builder->add('messageLanguage', LocaleType::class, [
            'label' => $this->__('Message language') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 7,
                'class' => ' validate-nospace',
                'title' => $this->__('Choose the message language of the message')
            ],
            'required' => false,
            'placeholder' => $this->__('All'),
            'choices' => $this->localeApi->getSupportedLocaleNames(),
            'choices_as_values' => true
        ]);
        
        $builder->add('allowComments', CheckboxType::class, [
            'label' => $this->__('Allow comments') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('allow comments ?')
            ],
            'required' => false,
        ]);
        
        $builder->add('imageUpload2', UploadType::class, [
            'label' => $this->__('Image upload 2') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the image upload 2 of the message')
            ],
            'required' => false && $options['mode'] == 'create',
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => '200k'
        ]);
        
        $builder->add('imageUpload3', UploadType::class, [
            'label' => $this->__('Image upload 3') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the image upload 3 of the message')
            ],
            'required' => false && $options['mode'] == 'create',
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => '200k'
        ]);
        
        $builder->add('imageUpload4', UploadType::class, [
            'label' => $this->__('Image upload 4') . ':',
            'attr' => [
                'class' => ' validate-upload',
                'title' => $this->__('Enter the image upload 4 of the message')
            ],
            'required' => false && $options['mode'] == 'create',
            'entity' => $options['entity'],
            'allowed_extensions' => 'gif, jpeg, jpg, png',
            'allowed_size' => '200k'
        ]);
        
        $builder->add('startDate', DateTimeType::class, [
            'label' => $this->__('Start date') . ':',
            'attr' => [
                'class' => ' validate-daterange-message',
                'title' => $this->__('Enter the start date of the message')
            ],
            'required' => false,
            'empty_data' => '',
            'with_seconds' => true,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text'
        ]);
        
        $builder->add('noEndDate', CheckboxType::class, [
            'label' => $this->__('No end date') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('no end date ?')
            ],
            'required' => false,
        ]);
        
        $builder->add('endDate', DateTimeType::class, [
            'label' => $this->__('End date') . ':',
            'attr' => [
                'class' => ' validate-daterange-message',
                'title' => $this->__('Enter the end date of the message')
            ],
            'required' => false,
            'empty_data' => '',
            'with_seconds' => true,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text'
        ]);
        
        $builder->add('weight', IntegerType::class, [
            'label' => $this->__('Weight') . ':',
            'empty_data' => '1',
            'attr' => [
                'maxlength' => 2,
                'class' => '',
                'title' => $this->__('Enter the weight of the message.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => false,
            'scale' => 0
        ]);
    }

    /**
     * Adds fields for attributes.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addAttributeFields(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['attributes'] as $attributeName => $attributeValue) {
            $builder->add('attributes' . $attributeName, TextType::class, [
                'mapped' => false,
                'label' => $this->__($attributeName),
                'attr' => [
                    'maxlength' => 255
                ],
                'data' => $attributeValue,
                'required' => false
            ]);
        }
    }

    /**
     * Adds a categories field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addCategoriesField(FormBuilderInterface $builder, array $options)
    {
        $builder->add('categories', CategoriesType::class, [
            'label' => $this->__('Categories') . ':',
            'empty_data' => [],
            'attr' => [
                'class' => 'category-selector'
            ],
            'required' => false,
            'multiple' => true,
            'module' => 'MUNewsModule',
            'entity' => 'MessageEntity',
            'entityCategoryClass' => 'MU\NewsModule\Entity\MessageCategoryEntity'
        ]);
    }

    /**
     * Adds a field for additional notification remarks.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addAdditionalNotificationRemarksField(FormBuilderInterface $builder, array $options)
    {
        $helpText = '';
        if ($options['is_moderator']) {
            $helpText = $this->__('These remarks (like a reason for deny) are not stored, but added to any notification emails send to the creator.');
        } elseif ($options['is_creator']) {
            $helpText = $this->__('These remarks (like questions about conformance) are not stored, but added to any notification emails send to our moderators.');
        }
    
        $builder->add('additionalNotificationRemarks', TextareaType::class, [
            'mapped' => false,
            'label' => $this->__('Additional remarks'),
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $helpText
            ],
            'attr' => [
                'title' => $options['mode'] == 'create' ? $this->__('Enter any additions about your content') : $this->__('Enter any additions about your changes')
            ],
            'required' => false,
            'help' => $helpText
        ]);
    }

    /**
     * Adds special fields for moderators.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addModerationFields(FormBuilderInterface $builder, array $options)
    {
        if (!$options['has_moderate_permission']) {
            return;
        }
    
        $builder->add('moderationSpecificCreator', UserLiveSearchType::class, [
            'mapped' => false,
            'label' => $this->__('Creator') . ':',
            'attr' => [
                'maxlength' => 11,
                'title' => $this->__('Here you can choose a user which will be set as creator')
            ],
            'empty_data' => 0,
            'required' => false,
            'help' => $this->__('Here you can choose a user which will be set as creator')
        ]);
        $builder->add('moderationSpecificCreationDate', DateTimeType::class, [
            'mapped' => false,
            'label' => $this->__('Creation date') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('Here you can choose a custom creation date')
            ],
            'empty_data' => '',
            'required' => false,
            'with_seconds' => true,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
            'help' => $this->__('Here you can choose a custom creation date')
        ]);
    }

    /**
     * Adds the return control field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addReturnControlField(FormBuilderInterface $builder, array $options)
    {
        if ($options['mode'] != 'create') {
            return;
        }
        $builder->add('repeatCreation', CheckboxType::class, [
            'mapped' => false,
            'label' => $this->__('Create another item after save'),
            'required' => false
        ]);
    }

    /**
     * Adds submit buttons.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['actions'] as $action) {
            $builder->add($action['id'], SubmitType::class, [
                'label' => $action['title'],
                'icon' => ($action['id'] == 'delete' ? 'fa-trash-o' : ''),
                'attr' => [
                    'class' => $action['buttonClass']
                ]
            ]);
        }
        $builder->add('reset', ResetType::class, [
            'label' => $this->__('Reset'),
            'icon' => 'fa-refresh',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
        $builder->add('cancel', SubmitType::class, [
            'label' => $this->__('Cancel'),
            'icon' => 'fa-times',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'munewsmodule_message';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => 'MU\NewsModule\Entity\MessageEntity',
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createMessage();
                },
                'error_mapping' => [
                    'isApproverUserValid' => 'approver',
                    'imageUpload1' => 'imageUpload1.imageUpload1',
                    'imageUpload2' => 'imageUpload2.imageUpload2',
                    'imageUpload3' => 'imageUpload3.imageUpload3',
                    'imageUpload4' => 'imageUpload4.imageUpload4',
                    'isStartDateBeforeEndDate' => 'startDate',
                ],
                'mode' => 'create',
                'attributes' => [],
                'is_moderator' => false,
                'is_creator' => false,
                'actions' => [],
                'has_moderate_permission' => false,
                'translations' => [],
            ])
            ->setRequired(['entity', 'mode', 'actions'])
            ->setAllowedTypes('mode', 'string')
            ->setAllowedTypes('attributes', 'array')
            ->setAllowedTypes('is_moderator', 'bool')
            ->setAllowedTypes('is_creator', 'bool')
            ->setAllowedTypes('actions', 'array')
            ->setAllowedTypes('has_moderate_permission', 'bool')
            ->setAllowedTypes('translations', 'array')
            ->setAllowedValues('mode', ['create', 'edit'])
        ;
    }
}
