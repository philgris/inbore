<?php

namespace App\Form\Type;

use App\Entity\Track;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class GpsBatchType extends AbstractType
{
    const ACTION_SET_TRACK          = 'action-set-track';
    const ACTION_UNSET_TRACK        = 'action-unset-track';
    const ACTION_DELETE             = 'action-delete';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('all', CheckboxType::class, [
                'label'                 => '',
                'required'              => false,
                'mapped'                => false,
                'attr'                  => [
                    'class'    => 'batch-all'
                ]
            ])
            ->add('action',          ChoiceType::class,
                [
                    'label'                 => 'batch.actions',
                    'mapped'                => false,
                    'placeholder'           => '',
                    'choices'               => [
                        'batch.gps.set_track'      => self::ACTION_SET_TRACK,
                        'batch.gps.unset_track'    => self::ACTION_UNSET_TRACK,
                        'batch.gps.delete'         => self::ACTION_DELETE
                    ],
                    'attr'                  => [
                        'class'     => 'form-control batch-action'
                    ]
                ]
            )

            -> add('track', EntityType::class,
                [
                    'class'         => 'App:Track',
                    'placeholder'   => '',
                    'choice_label'  => function(Track $track) {
                        return $track->getNumber();
                    },
                    'attr'                  => [
                        'class'    => 'form-control batch-target track-selector'
                    ]
                ]
            )

            ->add('ids', EntityType::class,
                [
                    'class'         => 'App:Gps',
                    'choice_label'  => 'id',
                    'mapped'        => false,
                    'required'      => false,
                    'multiple'      => true,
                    'expanded'      => true,
                ]
            )
        ;
    }

}