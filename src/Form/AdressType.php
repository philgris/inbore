<?php

namespace App\Form;

use App\Entity\Adress;
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



class AdressType extends ActionFormType { 
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
      /** // EmbedType de type N-N ; entityRelN-Ns : array Collection définit dans             ->add('entityRelN-Ns', CollectionType::class, [
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
    
        // name : typeOptions['type'] =  , typeOptions['options_code'] =     
                    ->add('name')
                
        // number : typeOptions['type'] =  , typeOptions['options_code'] =     
                    ->add('number')
                
        // postalCode : typeOptions['type'] =  , typeOptions['options_code'] =     
                    ->add('postalCode')
                
        // dateCre : typeOptions['type'] =  , typeOptions['options_code'] =     
        
                
        // dateMaj : typeOptions['type'] =  , typeOptions['options_code'] =     
        
                
        // userCre : typeOptions['type'] =  , typeOptions['options_code'] =     
        
                
        // userMaj : typeOptions['type'] =  , typeOptions['options_code'] =     
        
                
                      
                ->addEventSubscriber($this->addUserDate);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
                    'data_class' => Adress::class,
            ]);
    }
}
