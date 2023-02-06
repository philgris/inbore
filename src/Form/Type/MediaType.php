<?php

namespace App\Form\Type;

use App\Entity\Media;
use App\Form\EventListener\AddUserDateFields;
use App\Form\Type\FileUploaderType;
use App\Repository\Core\MediaRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// keep use of Type you need for forms fields 
use App\Form\ActionFormType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;


class MediaType extends ActionFormType
{
    /**
     * InBORe : MediaType / FileLoaderType dependencie
     * {@inheritdoc}
     */

    private $mediaRepository;
    private $uploader;
    private $config;
    private $twig;

    public function __construct(
        AddUserDateFields $addUserDate,
        Security $security,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        MediaRepository $mediaRepository,
        FileUploader $fileUploader,
        ParameterBagInterface $config,
        Environment $twig
    )
    {
        parent::__construct($addUserDate, $security, $em, $translator);
        $this->mediaRepository = $mediaRepository;
        $this->uploader = $fileUploader;
        $this->config = $config->get('admin');
        $this->twig = $twig;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $relativeRecord = $builder->getData();


        $builder->add('file', FileUploaderType::class, [
            'mapped'        => false,
            'required'      => false,
            'data'          => $builder->getData(),
            'action_type'   => $options['action_type'],
            'mime_types'    => ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'video/mp4'],
            'max_size'      => '8192m',
            'accept'        => '.jpg,.jpeg,.png,.gif,.mp4',
            'setters'       => [
                'width'     => 'setWidth',
                'height'    => 'setHeight',
                'size'      => 'setSize',
                'mime'      => 'setMimeType',
                'path'      => 'setPath',
                'filename'  => 'setFilename'
            ],
            'ignore_field_name' => true
        ]);

        $builder
            // name : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('name', null, ['label' => 'media.name'])
            // path : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('path', null, ['attr' => ['readonly'=>'readonly']])
            // filename : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('filename', null, ['attr' => ['readonly'=>'readonly']])
            // mimeType : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('mimeType', null, ['attr' => ['readonly'=>'readonly']])
            // size : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('size', null, ['attr' => ['readonly'=>'readonly']])
            // width : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('width', null, ['attr' => ['readonly'=>'readonly']])
            // height : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('height', null, ['attr' => ['readonly'=>'readonly']])
            // credit : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('credit')
            // license : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('license')
            // uriOldFile : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('uriOldFile')
            // comment : typeOptions['type'] =  , typeOptions['options_code'] =
            ->add('comment');
        $builder->addEventSubscriber($this->addUserDate);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
