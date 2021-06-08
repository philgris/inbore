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


/** Add use statements for embeded forms ex :
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
        /** SearchableSelectType : Auto complete fields from linked_entityFk : see /assets/Core/forms/InBORe_entity-name.js for js example
            ->add('linked-entityFk', SearchableSelectType::class, [
                'class' => 'App:Linked-entity',
                'choice_label' => 'code',
                'placeholder' => $this->translator->trans("Linked-entity typeahead placeholder"),
                'attr' => [
                    "maxlength" => "255",
                    'readonly' => ($options['action_type'] == Action::create() && $relativeRecord->getLinked-entityFk()),
                ],]) 
        */
        // name : typeOptions['type'] =  , typeOptions['options_code'] =     
                    ->add('name')
                
        // number : typeOptions['type'] =  , typeOptions['options_code'] =     
                    ->add('number')
                
        // postalCode : typeOptions['type'] =  , typeOptions['options_code'] =     
                    ->add('postalCode')
                         
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
