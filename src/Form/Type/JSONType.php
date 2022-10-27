<?php
/**
 * @author Philippe Bertin <contact@philippebertin.com>
 */

namespace App\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class JSONType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['keys'] as $infos) {
            if ($infos instanceof FormBuilderInterface) {
                $builder->add($infos);
            } else {
                list($name, $type, $options) = $infos;

                if (is_callable($options)) {
                    $extra = array_slice($infos, 3);

                    $options = $options($builder, $name, $type, $extra);

                    if ($options === null) {
                        $options = array();
                    } elseif (!is_array($options)) {
                        throw new \RuntimeException('the closure must return null or an array');
                    }
                }

                $builder->add($name, $type, $options);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'keys' => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix():string
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
