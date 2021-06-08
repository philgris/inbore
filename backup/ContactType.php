<?php

namespace App\Form;

use App\Entity\Contact;
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


/** Add use statements for embeded forms ex :
use App\Form\EmbedTypes\Name_of_embed_formEmbedType;
*/
use App\Form\EmbedTypes\TypecontactvocEmbedType;



class ContactType extends ActionFormType { 
/**
 * InBORe : template Type.tpl.php
 * {@inheritdoc}
 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $relativeRecord = $builder->getData();
        $builder
        /** SearchableSelectType : Auto complete fields from linked_entityFk : see /assets/Core/forms/InBORe_entity-name.js for js example */
            ->add('adressFk', SearchableSelectType::class, [
                'class' => 'App:Adress',
                'choice_label' => 'name',
                'placeholder' => $this->translator->trans("Adress typeahead placeholder"),
                'attr' => [
                    "maxlength" => "255",
                    'readonly' => ($options['action_type'] == Action::create() && $relativeRecord->getAdressFk()),
                ],]) 
        // nom : typeOptions['type'] =  , typeOptions['options_code'] =     
                    ->add('nom')
                
        // date : typeOptions['type'] =  , typeOptions['options_code'] =     
                ->add('date', DateFormattedType::class)
                
        // no : typeOptions['type'] =  , typeOptions['options_code'] =     
                    ->add('no')
        //
                    ->add('typecontactvocs', CollectionType::class, [
                            'entry_type' => TypecontactvocEmbedType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'prototype' => true,
                            'prototype_name' => '__name__',
                            'by_reference' => false,
                            'entry_options' => [
                              "label" => false,
                            ],
                          ])
                             
                ->addEventSubscriber($this->addUserDate);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
                    'data_class' => Contact::class,
            ]);
    }
}
