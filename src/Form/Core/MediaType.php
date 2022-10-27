<?php

namespace App\Form\Core;

use App\Entity\Core\Media;
use App\Form\EventListener\AddUserDateFields;
use App\Form\Type\HTMLType;
use App\Repository\Core\MediaRepository;
use App\Services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;


/** // Add use statements for embeded forms ex :
 * use App\Form\EmbedTypes\Name_of_embed_formEmbedType;
 */
class MediaType extends ActionFormType
{
    /**
     * InBORe : template Type.tpl.php
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

        //upload image
        if ($options['action_type']->getValue() == 'new' || $options['action_type']->getKey() == 'edit') {
            $builder->add($this->config['media']['upload']['field'], FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => $this->config['media']['upload']['mimes'],
                        'maxSize'   => $this->config['media']['upload']['max_size']
                    ]),
                ],
                'attr' => [
                    'onchange' => '$(this).next(\'.custom-file-label\').html($(this).val().split(\'\\\\\').pop())',
                    'accept' => $this->config['media']['upload']['accept']
                ]
            ]);
        }

        if ($options['action_type']->getValue() == 'show' || $options['action_type']->getKey() == 'edit') {
            // if file exists show a link
            if (
                $builder->getData() && $builder->getData()->getFilename() &&
                $this->uploader->fileExists($builder->getData())
            ) {
                $mime = $this->uploader->getMime($builder->getData());
                if (preg_match('/\/admin\//', $_SERVER['REQUEST_URI'])) {
                    if (preg_match('/^image\//', $mime)) {
                        $template =
                            '<a href="{{ path(\'admin_file\', {\'entity\':\'media\', \'id\':' . $builder->getData()->getId() . '}) }}" target="_blank">' .
                            '<img src="{{ path(\'admin_file\', {\'entity\':\'media\', \'id\':' . $builder->getData()->getId() . '}) }}" alt="' . $builder->getData()->getFilename() . '" style="max-width:100%; max-height:500px;">' .
                            '</a>';
                    } elseif (preg_match('/^video\//', $mime)) {
                        $template =
                            '<video controls style="max-width:100%; max-height:500px;">' .
                            '<source type="' . $mime . '" src="{{ path(\'admin_file\', {\'entity\':\'media\', \'id\':' . $builder->getData()->getId() . '}) }}"></source>' .
                            '</video>' .
                            '<a href="{{ path(\'admin_file\', {\'entity\':\'media\', \'id\':' . $builder->getData()->getId() . '}) }}" target="_blank">' .
                            '<i class="fa fa-file-video"></i> ' . $builder->getData()->getFilename() .
                            '</a>';
                    } else {
                        $template =
                            '<a href="{{ path(\'admin_file\', {\'entity\':\'media\', \'id\':' . $builder->getData()->getId() . '}) }}" target="_blank">' .
                            '<i class="fa fa-file"></i> ' . $builder->getData()->getFilename() .
                            '</a>';
                    }
                } else {
                    if (preg_match('/^image\//', $mime)) {
                        $template =
                            '<a href="{{ path(\'media_file\', {\'id\':' . $builder->getData()->getId() . '}) }}" target="_blank">' .
                            '<img src="{{ path(\'media_file\', {\'id\':' . $builder->getData()->getId() . '}) }}" alt="' . $builder->getData()->getFilename() . '" style="max-width:100%; max-height:500px;">' .
                            '</a>';
                    } elseif (preg_match('/^video\//', $mime)) {
                        $template =
                            '<video controls style="max-width:100%; max-height:500px;">' .
                            '<source type="' . $mime . '" src="{{ path(\'media_file\', {\'id\':' . $builder->getData()->getId() . '}) }}"></source>' .
                            '</video>' .
                            '<a href="{{ path(\'media_file\', {\'id\':' . $builder->getData()->getId() . '}) }}" target="_blank">' .
                            '<i class="fa fa-file-video"></i> ' . $builder->getData()->getFilename() .
                            '</a>';
                    } else {
                        $template =
                            '<a href="{{ path(\'media_file\', {\'id\':' . $builder->getData()->getId() . '}) }}" target="_blank">' .
                            '<i class="fa fa-file"></i> ' . $builder->getData()->getFilename() .
                            '</a>';
                    }
                }

                $builder->add('link', HTMLType::class, [
                    'label' => ' ',
                    'mapped' => false,
                    'required' => false,
                    'attr' => ['raw' => true],
                    'data' => $this->twig->createTemplate($template)->render()
                ]);
            }
        }
        //

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
