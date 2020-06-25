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

namespace MU\NewsModule\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use MU\NewsModule\Entity\Factory\EntityFactory;
use MU\NewsModule\Entity\ImageEntity;
use MU\NewsModule\Form\Type\Field\UploadType;
use MU\NewsModule\Helper\UploadHelper;

/**
 * Image editing form type implementation class.
 */
class ImageType extends AbstractType
{
    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var UploadHelper
     */
    protected $uploadHelper;

    public function __construct(
        EntityFactory $entityFactory,
        UploadHelper $uploadHelper
    ) {
        $this->entityFactory = $entityFactory;
        $this->uploadHelper = $uploadHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $imageEntity = $this->entityFactory->createImage(); // TODO ??
        $builder->add('theFile', UploadType::class, [
            'label' => 'The file:',
            'attr' => [
                'accept' => '.' . implode(',.', $this->uploadHelper->getAllowedFileExtensions('image', 'theFile')),
                'class' => ' validate-upload',
                'title' => 'Enter the the file of the image.'
            ],
            'required' => true && 'create' === $options['mode'],
            'entity' => $imageEntity,
            'allow_deletion' => true,
            'allowed_extensions' => implode(', ', $this->uploadHelper->getAllowedFileExtensions('image', 'theFile')),
            'allowed_size' => ''
        ]);
        $builder->add('caption', TextType::class, [
            'label' => 'Caption:',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => 'Enter the caption of the image.'
            ],
            'required' => false,
        ]);
        $builder->add('sortNumber', IntegerType::class, [
            'label' => 'Sort number:',
            'help' => 'Note: this value must not be lower than %minValue%.',
            'help_translation_parameters' => ['%minValue%' => 1],
            'empty_data' => 1,
            'attr' => [
                'maxlength' => 4,
                'class' => '',
                'min' => 1,
                'title' => 'Enter the sort number of the image. Only digits are allowed.'
            ],
            'required' => true
        ]);
    }

    public function getBlockPrefix()
    {
        return 'munewsmodule_image';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => ImageEntity::class,
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createImage();
                },
                'error_mapping' => [
                    'theFile' => 'theFile.theFile',
                ],
                'mode' => 'create'
            ])
            ->setRequired(['mode'])
            ->setAllowedTypes('mode', 'string')
            ->setAllowedValues('mode', ['create', 'edit'])
        ;
    }
}