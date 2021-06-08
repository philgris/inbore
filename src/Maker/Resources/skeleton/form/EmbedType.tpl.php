<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//
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



class <?= $class_name ?> extends AbstractType { 
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
      
    // keep the fields to show in the embed Form 
    <?php foreach ($form_fields as $form_field => $typeOptions): ?>
    // <?= $form_field ?> : typeOptions['type'] = <?= $typeOptions['type']?> , typeOptions['options_code'] = <?=$typeOptions['options_code']?>    
    <?php if ($form_field ==  'dateCre' || $form_field ==  'dateMaj' || $form_field ==  'userCre' || $form_field ==  'userMaj' ): ?>    
    <?php elseif (null === $typeOptions['type'] && !$typeOptions['options_code']): ?>
        ->add('<?= $form_field ?>')
    <?php elseif (null !== $typeOptions['type'] && !$typeOptions['options_code']): ?>
        ->add('<?= $form_field ?>', <?= $typeOptions['type'] ?>::class)
    <?php else: ?>
        ->add('<?= $form_field ?>', <?= $typeOptions['type'] ? ($typeOptions['type'].'::class') : 'null' ?>, [
    <?= $typeOptions['options_code']."\n" ?>
                ])
    <?php endif; ?>            
    <?php endforeach; ?>                  

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
    <?php if ($bounded_full_class_name): ?>
                'data_class' => 'App\Entity\<?= $bounded_class_name ?>',
    <?php else: ?>
                // Configure your form options here
    <?php endif ?>
        ]);
    }
}
