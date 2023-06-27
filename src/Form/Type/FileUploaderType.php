<?php
/**
 * @author Philippe Bertin <contact@philippebertin.com>
 */

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Services\FileUploader;
use Symfony\Component\Validator\Constraints\File;
use App\Form\Enums\Action;
use Twig\Environment;

class FileUploaderType extends AbstractType
{
    private $uploader;
    private $twig;

    public function __construct(
        FileUploader $fileUploader,
        Environment $twig
    )
    {
        $this->uploader = $fileUploader;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['action_type'] == Action::create->value || $options['action_type'] == Action::edit->value) {
            $opts = [
                'label'         => false,
                'mapped'        => false,
                'required'      => false
            ];

            // add file constraints if max_size and/or mime_types options are given
            $constraints = [];
            if (isset($options['max_size'])) {
                $constraints['maxSize'] = $options['max_size'];
            }
            if (isset($options['mime_types'])) {
                $constraints['mimeTypes'] = $options['mime_types'];
            }
            if (!empty($constraints)) {
                $opts['constraints'] = new File($constraints);
            }

            $attr = ['onchange'  => '$(this).next(\'.custom-file-label\').html($(this).val().split(\'\\\\\').pop())'];

            // add accept html attribute if accept option is given
            if (isset($options['accept'])) {
                $attr['accept'] = $options['accept'];
            }

            $opts['attr'] = $attr;
            $builder->add($builder->getName().FileUploader::FILE_SUFFIX, FileType::class, $opts);
        }

        if ($options['action_type'] == Action::show->value || $options['action_type'] == Action::edit->value) {
            $object = $builder->getData();
            $field = $options['ignore_field_name'] ? null: $builder->getName();
            // if file exists and file_path option is set show a link
            if (
                $builder->getData() &&
                $this->uploader->fileExists($object, $field)
            ) {
                $mime = $this->uploader->getMime($object, $field);
                $url = $this->uploader->getFileUrl($object, $field);

                if (preg_match('/^image\//', $mime) && $url) {
                    $template =
                        '<a href="'.$url.'" target="_blank">'.
                            '<img src="'.$url.'" alt="' . $this->uploader->getFilename($builder->getData(), $field) . '" style="max-width:100%; max-height:50px;">'.
                        '</a>';
                } elseif (preg_match('/^video\//', $mime) && $url) {
                    $template =
                        '<video controls style="max-width:100%; max-height:500px;">' .
                            '<source type="' . $mime . '" src="'.$url.'"></source>' .
                        '</video>';
                } elseif ($url) {
                    $template =
                        '<a href="'.$url.'" target="_blank">'.
                            '<i class="fas fa-file"></i> ' . $this->uploader->getFilename($builder->getData(), $field) .
                        '</a>';
                } elseif (isset($options['file_path'])) {
                    $template =
                        '<a href="{{ path(\''.$options['file_path'].'\', {\'id\':\'' . $object->getId() . '\', \'field\':\'' . $field .'\'}) }}" target="_blank">'.
                            '<i class="fas fa-file"></i> ' . $this->uploader->getFilename($builder->getData(), $field).
                        '</a>';
                } else {
                    $template =
                        '<i class="fas fa-file"></i> ' . $this->uploader->getFilename($builder->getData(), $field);
                }
                try {
                    $data = $this->twig->createTemplate($template)->render();
                } catch(\Exception $e) {
                    $data = $e->getMessage();
                }
                $builder->add($builder->getName().FileUploader::FILENAME_SUFFIX, HTMLType::class, [
                    'label'         => false,
                    'mapped'        => false,
                    'required'      => false,
                    'attr'          => ['raw' => true],
                    'data'          => $data
                ]);

                if ($options['action_type'] == Action::edit->value) {
                    $builder->add($builder->getName().FileUploader::REMOVE_SUFFIX, CheckboxType::class, [
                        'label'         => '<i class="fas fa-trash-alt"></i>',
                        'label_html'    => true,
                        'mapped'        => false,
                        'required'      => false,
                        'attr'          => ['class' => 'form-control'],
                        'label_attr'    => ['class' => 'checkbox-inline switch-custom'],
                        //'help'  => 'help'
                    ]);
                }
            }
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['class'] = isset($view->vars['attr']['class'])
            ? $view->vars['attr']['class']. ' file-uploader-group'
            : 'file-uploader-group'
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'entity_name_in_config' => null,
            'action_type'           => Action::show->value,
            'inherit_data'          => true,
            'file_path'             => null,    // optional: force a specific path
            'mime_types'            => null,
            'accept'                => null,
            'max_size'              => null,
            'setters'               => null,    // ex: ['width' => 'setWidth', 'height' => 'setHeight'] will call setWidth and setHeight methods
            'ignore_field_name'     => null     // by default file is saved in a folder having the name of the related field. If true file IS NOT in a separated folder
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix():string
    {
        return FileUploader::BLOCK_PREFIX;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

}