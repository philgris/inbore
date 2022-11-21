<?= "<?php\n" ?>

namespace <?= $namespace ?>;

<?php if ($bounded_full_class_name): ?>
use <?= $bounded_full_class_name ?>;
<?php endif ?>
<?php foreach ($field_type_use_statements as $className): ?>
use <?= $className ?>;
<?php endforeach; ?>
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
<?php foreach ($constraint_use_statements as $className): ?>
use <?= $className ?>;
<?php endforeach; ?>
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

/** // add the LinkEntity call by EntityType
use App\Entity\LinkEntity;
*/

// keep use of Type you need for forms fields 
use App\Form\Type\ActionFormType;;
use App\Form\Type\ModalButtonType;
use App\Form\Enums\Action;


/** // Add use statements for embeded forms ex :
use App\Form\EmbedTypes\Name_of_embed_formEmbedType;
*/



class <?= $class_name ?> extends ActionFormType { 
/**
 * InBORe : template Type.tpl.php
 * {@inheritdoc}
 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $relativeRecord = $builder->getData();
        $builder            
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
        ->addEventSubscriber($this->addUserDate);
        ;
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
    <?php if ($bounded_full_class_name): ?>
                'data_class' => <?= $bounded_class_name ?>::class,
    <?php else: ?>
                // Configure your form options here
    <?php endif ?>
        ]);
    }
}
