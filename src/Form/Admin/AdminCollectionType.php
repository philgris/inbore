<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdminCollectionType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'id',
            EntityType::class,
            [
                'class'         => $options['data_class'],
                'choice_label'  => 'id',
                'placeholder'   => ''
            ]
        );
    }
}
