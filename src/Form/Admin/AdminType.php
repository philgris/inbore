<?php

namespace App\Form\Admin;

use App\Form\Type\ActionFormType;
use App\Form\EventListener\AddUserDateFields;
use App\Repository\Core\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;


class AdminType extends ActionFormType {

    private $repository;

    public function __construct(
        AddUserDateFields $addUserDate,
        Security $security,
        EntityManagerInterface $em,
        TranslatorInterface $translator,
        AdminRepository $repository
    )
    {
        parent::__construct($addUserDate, $security, $em, $translator);
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $object = $builder->getData();
        $a = explode('\\', get_class($object));
        $entity = strtolower(array_pop($a));
        $rc = new \ReflectionClass($object);
        foreach ($rc->getProperties() as $rp) {
            if (!in_array($rp->getName(), ['id', 'dateCre', 'dateMaj', 'userCre', 'userMaj', 'userAdmin', 'groupAdmin'])) {
                if(preg_match('/(ManyToOne).*targetEntity\s*=\s*"([^"]+)"/', $rp->getDocComment(), $matches)) {
                    $builder->add(
                        $rp->getName(),
                        EntityType::class,
                        [
                            'class'         => 'App:'.$matches[2],
                            'choice_label'  => 'id',
                            'placeholder'   => ''
                        ]
                    );
                } elseif(preg_match('/(OneToMany).*targetEntity\s*=\s*"([^"]+)"/', $rp->getDocComment(), $matches)) {
                    $builder->add(
                        $rp->getName(),
                        CollectionType::class,
                        [
                            'entry_type'    => AdminCollectionType::class,
                            'entry_options' => ['data_class' => 'App\\Entity\\'.$matches[2]],
                            'allow_add'     => true,
                            'allow_delete'  => true,
                            'prototype'     => true,
                            'by_reference'  => false,
                            'required'      => false,
                            'attr' => [
                                "data-allow-new"        => true,
                                "data-modal-controller" => 'App\\Controller\\Admin\\AdminController::newmodalAction',
                                "data-modal-entity"     => $entity,
                                "choice_label"          => 'filename',
                            ],
                        ]
                    );
                } else {
                    $builder->add($rp->getName());
                }
            }
        }
        $builder->addEventSubscriber($this->addUserDate);
    }
}
