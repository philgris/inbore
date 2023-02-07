<?php

namespace App\Form\EmbedTypes;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

// add Entity call by EntityType
use App\Entity\Media;


class MediaLinkEmbedType extends AbstractType { 
/**
 * InBORe : template EmbedType.tpl.php
 *
 * @param FormBuilderInterface $builder
 * @param array $options
 *
 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $relativeRecord = $builder->getData();
        $builder
                       
        // idMedia : typeOptions['type'] =  , typeOptions['options_code'] =     
            //->add('idMedia')
            ->add('idMedia', EntityType::class, array(
            'class' => 'App\\Entity\\Media',
            'query_builder' => function (EntityRepository $er) {
              return $er->createQueryBuilder('media')
                ->orderBy('media.filename', 'ASC');
            },
            'placeholder' => 'Choose a filename',
            'choice_label' => 'filename', // nom_field_to_order : name of field in the database
            'multiple' => false,
            'expanded' => false,
          ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            ]);
    }
}
