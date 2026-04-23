<?php

namespace App\Form\Core;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\Type\ActionFormType;
use Symfony\Component\Validator\Constraints\Regex;


class UserType extends ActionFormType {

  /**
   * {@inheritdoc} 
   */
  public function buildForm(FormBuilderInterface $builder, array $options): void {

    $isRole = null;
    if ($this->security->isGranted('ROLE_ADMIN')) {
        $isRole = $this->security->isGranted('ROLE_SUPER_ADMIN') ? "ROLE_SUPER_ADMIN" : "ROLE_ADMIN";
    }
    $isAdminForm = $builder->getData()->getRole() == "ROLE_ADMIN";
    $isSuperAdminForm = $builder->getData()->getRole() == "ROLE_SUPER_ADMIN";
    $passwordRequired = $builder->getData()->getId()==null || $builder->getData()->getPassword()==null;
    $builder
        ->add('username')
        ->add('plainPassword', TextType::class, [
              'label'       => 'Mot de passe',
              'help'        => '<span class="'.($passwordRequired ? 'text-danger' : 'text-muted').'">
                                    12 caractères minimum avec au moins: une minuscule, une majuscule, un nombre et un caractère spécial ( #?!@$%^&*- ).
                                </span>'
                                .(
                                    $passwordRequired
                                        ? ''
                                        : '<span class="text-muted">Laisser vide pour ne pas le modifier.</span>'
                                ),
              'help_html'   => true,
              'attr'        => ['value'=>''],
              'constraints' => new Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/'),
            'required'  => $passwordRequired
        ])
//      ->add('plainPassword', RepeatedType::class, array(
////        'type' => PasswordType::class,
//        'type' => TextType::class,
//
////        'first_options' => array('label' => 'Password'),
//          'first_options'   => [
//              'label'       => 'Mot de passe',
//              'help'        => '<span class="text-danger">8 caractères minimum, avec au moins : une minuscule, une majuscule, un nombre et un caractère spécial.</span>',
//              'help_html'   => true,
//              'attr'        => ['value'=>''],
//              'constraints' => new Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/')
//          ],
//        'second_options' => [
//            'label' => 'Confirmation du mot de passe',
//            'attr'  => ['value'=>'']
//        ]
//      ))
      ->add('email', EmailType::class, array(
//        'required' => false,
      ))
      ->add('name', TextType::class, [
          'label'       => 'Nom',
      ])
      ->add('institution')   ;

    // Affichage des formulaires User suivant les rôles 
    switch ($isRole) {
        case "ROLE_SUPER_ADMIN":
                    if ($isSuperAdminForm){ // edit ou show du formulaire SUPER_ADMIN
                        $builder->add('role', ChoiceType::class, array(
                          'disabled' => true ,
                          'choices' => array(
                              'SUPER_ADMIN'           => 'ROLE_SUPER_ADMIN',
                          ),
                          'required' => true,
                          'choice_translation_domain' => false,
                          'multiple' => false,
                          'expanded' => true,
                          'label_attr' => array('class' => 'radio-inline'),
                        )) ;
                    } else {
                        $builder->add('role', ChoiceType::class, array(
                          'disabled' => $isSuperAdminForm,
                          'choices' => array(
                                'ADMIN'           => 'ROLE_ADMIN',
                                'SENIOR'          => 'ROLE_SENIOR',
                                'JUNIOR'          => 'ROLE_JUNIOR',
                                'COLLABORATION'   => 'ROLE_COLLABORATION',
                                'INVITED'         => 'ROLE_INVITED',
                                'LOCKED'          => 'ROLE_LOCKED',              
                          ),
                          'required' => true,
                          'choice_translation_domain' => false,
                          'multiple' => false,
                          'expanded' => true,
                          'label_attr' => array('class' => 'radio-inline'),
                        ))  ;   
                    }
            break;
        case "ROLE_ADMIN":
                    if ($isAdminForm){ // edit ou show du formulaire ADMIN
                        $builder->add('role', ChoiceType::class, array(
                          'disabled' => true ,
                          'choices' => array(
                              'ADMIN'           => 'ROLE_ADMIN',
                          ),
                          'required' => true,
                          'choice_translation_domain' => false,
                          'multiple' => false,
                          'expanded' => true,
                          'label_attr' => array('class' => 'radio-inline'),
                        )) ;
                    } else {
                        if ($isSuperAdminForm){ // show du formulaire SUPER_ADMIN
                            $builder->add('role', ChoiceType::class, array(
                              'disabled' => true ,
                              'choices' => array(
                                  'SUPER_ADMIN'           => 'ROLE_SUPER_ADMIN',
                              ),
                              'required' => true,
                              'choice_translation_domain' => false,
                              'multiple' => false,
                              'expanded' => true,
                              'label_attr' => array('class' => 'radio-inline'),
                            )) ;
                        } else {
                            $builder->add('role', ChoiceType::class, array(
                              'disabled' => $isSuperAdminForm,
                              'choices' => array(
                                    'SENIOR'          => 'ROLE_SENIOR',
                                    'JUNIOR'          => 'ROLE_JUNIOR',
                                    'COLLABORATION'   => 'ROLE_COLLABORATION',
                                    'INVITED'         => 'ROLE_INVITED',
                                    'LOCKED'          => 'ROLE_LOCKED',              
                              ),
                              'required' => true,
                              'choice_translation_domain' => false,
                              'multiple' => false,
                              'expanded' => true,
                              'label_attr' => array('class' => 'radio-inline'),
                            ))  ;   
                        }
                    }
            break;
        default:
                        $builder->add('role', ChoiceType::class, array(
                          'disabled' => true,
                          'choices' => array(
                            'SENIOR'          => 'ROLE_SENIOR',
                            'JUNIOR'          => 'ROLE_JUNIOR',
                  //          'PROJECT'         => 'ROLE_PROJECT',
                            'COLLABORATION'   => 'ROLE_COLLABORATION',
                            'INVITED'         => 'ROLE_INVITED',
                            'LOCKED'          => 'ROLE_LOCKED',
                          ),
                          'required' => true,
                          'choice_translation_domain' => false,
                          'multiple' => false,
                          'expanded' => true,
                          'label_attr' => array('class' => 'radio-inline'),
                        ));  
            break;
    }    

      $builder 
      ->add('commentaireUser')
      ->addEventSubscriber($this->addUserDate);
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver): void {
    parent::configureOptions($resolver);
    $resolver->setDefaults(array(
      'data_class' => 'App\Entity\Core\User',
    ));
  }

}
