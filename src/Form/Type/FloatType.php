<?php
/**
 * @author Philippe Bertin <contact@philippebertin.com>
 */

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class FloatType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setNormalizer('attr', function (Options $options, $value) {
            return array_merge($value, ['pattern' => '^[0-9]*([\.,][0-9]*)?$']);
        });
    }

    public function getParent():?string
    {
        return NumberType::class;
    }
}
