<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Form\Type;

use Goteo\Model\Image;
use Goteo\Library\Text;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 *
 * This class creates a Symfony Form Type uploading files using Dropzone (needs assets/js/forms.js)
 *
 */
class DropfilesType extends FileType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new CallbackTransformer(
                function ($image) {
                    // TODO: for any type of file
                    if(is_array($image)) return $image;

                    if($image instanceOf File) return new Image($image);
                    if($image instanceOf Image) return $image;

                    return null;
                },
                function ($file) {
                    return $file;
                }
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => false,
            'data_class' => null,
            'empty_data' => null,
            'multiple' => false,
            'auto_process' => false, // auto process the sending of files
            'url' => null, // url parameter for dropzone (null implies default action)
            'limit' => 10, // Max number of files in multiple uploads
            'sortable' => true, // Allow dragndrop sort of multiple files
            'text_upload' => '<i style="font-size:2em" class="fa fa-plus"></i><br><br>' . Text::get('dashboard-project-dnd-image')
        ));

    }

    /**
     * {@inheritdoc}
     */
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if(is_array($view->vars['value'])) {
            $options['multiple'] = true;
        } else {
            $options['multiple'] = false;
            $options['limit'] = 1;

            $view->vars = array_replace($view->vars, array(
                'value' => [$view->vars['value']],
            ));
        }
        if ($options['multiple']) {
            $view->vars['full_name'] .= '[]';
            $view->vars['attr']['multiple'] = 'multiple';
        }
        $view->vars['text_upload'] = $options['text_upload'];
        $view->vars['limit'] = $options['limit'];
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['auto_process'] = $options['auto_process'];
        $view->vars['url'] = $options['url'] ? $options['url'] : $view->parent->vars['action'];


    }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function getParent()
    // {
    //     return FileType::class;
    // }

    /**
     * {@inheritdoc}
     */
    // public function getName()
    // {
    //     return $this->getBlockPrefix();
    // }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dropfiles';
    }
}
