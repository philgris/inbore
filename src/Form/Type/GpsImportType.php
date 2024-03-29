<?php

namespace App\Form\Type;

use App\Entity\Gps;
use App\Repository\Core\GpsRepository;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use App\Entity\Site;
// keep use of Type you need for forms fields 
use App\Form\Type\ActionFormType;
//
use Symfony\Component\Validator\Constraints\File;


class GpsImportType extends ActionFormType {
/**
 * InBORe : template Type.tpl.php
 * {@inheritdoc}
 */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $relativeRecord = $builder->getData();
        $builder    
                
        // Ex. idSite : Add Fk to Site Entity if Gps is linked to a Site ...   

        // file gpx
        ->add('file', FileType::class, [
            'mapped'        => false,
            'constraints'   => [
                new File([
                    'maxSize'   => GpsRepository::FILE_MAX_SIZE,
                    'mimeTypes' => GpsRepository::FILE_MIME_TYPES
                ]),
            ],
            'attr'         => [
                'onchange'  => '$(this).next(\'.custom-file-label\').html($(this).val().split(\'\\\\\').pop())',
                'accept'    => GpsRepository::FILE_ACCEPT_TYPES
            ]
        ])
        ->add('duplications', ChoiceType::class, [
            'mapped'        => false,
            'choices'       => [
                'import.gps.check_for_duplications' => GpsRepository::DUPLICATION_CHECK,
                'import.gps.without_duplications'   => GpsRepository::DUPLICATION_IGNORE,
                'import.gps.with_duplications'      => GpsRepository::DUPLICATION_IMPORT,
            ]
        ])

        // idTrack : typeOptions['type'] =  , typeOptions['options_code'] =     
        //    ->add('idTrack')
                                     
        ->addEventSubscriber($this->addUserDate);
        ;
    }

//    public function configureOptions(OptionsResolver $resolver)
//    {
//        parent::configureOptions($resolver);
//        $resolver->setDefaults([
//                    'data_class' => Gps::class,
//            ]);
//    }
}
