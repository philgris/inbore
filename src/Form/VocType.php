<?php

namespace App\Form;

use App\Entity\Voc;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

// keep use of Type you need for forms fields 
use App\Form\ActionFormType;
use App\Form\Type\BaseVocType;
use App\Form\Type\CountryVocType;
use App\Form\Type\DateFormattedType;
use App\Form\Type\DatePrecisionType;
use App\Form\Type\EntityCodeType;
use App\Form\Type\GeneSpecimenType;
use App\Form\Type\GeneType;
use App\Form\Type\ModalButtonType;
use App\Form\Type\SearchableSelectType;
use App\Form\Type\SequenceStatusType;
use App\Form\Type\TaxnameType;
use App\Form\Enums\Action;


/** // Add use statements for embeded forms ex :
use App\Form\EmbedTypes\Name_of_embed_formEmbedType;
*/



class VocType extends ActionFormType { 
/**
 * InBORe : template Type.tpl.php
 * {@inheritdoc}
 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $relativeRecord = $builder->getData();
        $builder
        /** // SearchableSelectType : Auto complete fields from linked_entityFk : see /assets/Core/forms/InBORe_entity-name.js for js example
            ->add('linked-entityFk', SearchableSelectType::class, [
                'class' => 'App:Linked-entity',
                'choice_label' => 'code',
                'placeholder' => $this->translator->trans("Linked-entity typeahead placeholder"),
                'attr' => [
                    "maxlength" => "255",
                    'readonly' => ($options['action_type'] == Action::create() && $relativeRecord->getLinked-entityFk()),
                ],]) 
        */
        /** // DateFormattedType like JJ-MM-AAAA
           ->add('nameOfdateField', DateFormattedType::class)
        */
        /** // ChoiceType widget like radio-inline button
              ->add('nameOfChoiceField', ChoiceType::class, array(
                'choices' => array('exChoice1' => valueChoice1 , 'exCHoice2' => valueOfChoice2),
                'required' => true,
                'choice_translation_domain' => true,
                'multiple' => false,
                'expanded' => true,
                'label_attr' => array('class' => 'radio-inline'),
                ))
      */
      /** // IntegerType
            ->add('nameOfIntergerField', IntegerType::class, [
            'attr' => ["min" => "0"],
            'required' => false,
      ])
      */
      /** // BaseVocType ex.
        ->add('samplingMethodVocFk', BaseVocType::class, [
          'voc_parent' => 'samplingMethod',
          'placeholder' => 'Choose a Sampling method',
          ])
      */
      /** // EmbedType de type N-N ; entityRelN-Ns : array Collection defined in the Entity
        ->add('entityRelN-Ns', CollectionType::class, [
            'entry_type' => EntityRelN-NEmbedType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'prototype_name' => '__name__',
            'by_reference' => false,
            'entry_options' => [
            'label' => false,
            ],
          ])
    */
    /** // EmbedType de type N-N with modal windows to create on-fly record ; entityRelN-Ns : array Collection defined in the Entity
        ->add('entityRelN-Ns', CollectionType::class, [
            'entry_type' => EntityRelN-NEmbedType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'prototype_name' => '__name__',
            'by_reference' => false,
            'attr' => [
              "data-allow-new" => true,
              "data-modal-controller" => 'App\\Controller\\Core\\EntityRelN1Controller::newmodalAction',
              "choice_label" => 'NAME_OF_Choice_label_USED_IN_EntityRelN-NEmbedType',
            ],
            'entry_options' => [
              'label' => false,
            ],
          ])
    */
    
        // code : typeOptions['type'] =  , typeOptions['options_code'] =     
            ->add('code')
                
        // parent : typeOptions['type'] =  , typeOptions['options_code'] =     
            ->add('parent')
                
        // libelle : typeOptions['type'] =  , typeOptions['options_code'] =     
            ->add('libelle')
                
                      
        //->addEventSubscriber($this->addUserDate);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
                    'data_class' => Voc::class,
            ]);
    }
}
