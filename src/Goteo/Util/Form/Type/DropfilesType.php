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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;

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
        // Current files
        $builder->add('current', FileType::class, [
            'multiple' => true,
            'data' => is_array($options['data']) ? $options['data'] : [$options['data']],
            'data_class' => null
        ]);

        $builder->get('current')
            ->addModelTransformer(new $options['model_transformer']);

        // New added files
        $builder->add('uploads', FileType::class, [
            'multiple' => true
        ]);

        $builder->get('uploads')
            ->addModelTransformer(new $options['upload_transformer']);

        // General processing
        $builder->addViewTransformer(new CallbackTransformer(
            function($image) {
                return is_array($image) ? $image : [$image];
            },
            function ($image) {
                // var_dump($image);die;
                // Sum current + uploads
                $img = isset($image['current']) && is_array($image['current']) ? $image['current'] : [];
                if($image['uploads']) {
                    if(is_array($image['uploads'])) {
                        $img = array_merge($img, $image['uploads']);
                    }
                }
                // var_dump($img);die;
                return $img;
                // return null;
            }));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            // 'compound' => false,
            'data_class' => null,
            'markdown_link' => '', // creates a button to send the image link to a markdown editor
            'empty_data' => null,
            'multiple' => true,
            'accepted_files' => null, // Eg.: image/*,application/pdf,.psd
            'url' => null, // url parameter for dropzone (null implies no AJAX upload (form must be sent manually))
            'limit' => 10, // Max number of files in multiple uploads
            'sortable' => true, // Allow dragndrop sort of multiple files
            'text_upload' => '<i style="font-size:2em" class="fa fa-plus"></i><br><br>' . Text::get('dashboard-project-dnd-image'),
            'text_delete_image' => Text::get('dashboard-project-delete-image'),
            'text_send_to_markdown' => Text::get('dashboard-project-send-to-markdown'),
            'text_max_files_reached' => Text::get('dashboard-max-files-reached'),
            'text_file_type_error' => Text::get('dashboard-file-type-error'),
            'text_download' => Text::get('regular-download'),
            'row_class' => '',
            'model_transformer' => 'Goteo\Util\Form\DataTransformer\ModelImageTransformer',
            'upload_transformer' => 'Goteo\Util\Form\DataTransformer\UploadImageTransformer',
            'type' => 'image'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // var_dump($view->vars);die;
        if(!is_array($view->vars['data'])) {
            $options['limit'] = 1;
        }
        // var_dump($options);die;
        $options['multiple'] = true;
        $view->vars['attr']['multiple'] = 'multiple';
        $view->vars['markdown_link'] = $options['markdown_link'];
        $view->vars['text_delete_image'] = $options['text_delete_image'];
        $view->vars['text_send_to_markdown'] = $options['text_send_to_markdown'];
        $view->vars['text_max_files_reached'] = $options['text_max_files_reached'];
        $view->vars['text_file_type_error'] = $options['text_file_type_error'];
        $view->vars['text_download'] = $options['text_download'];
        $view->vars['text_upload'] = $options['text_upload'];
        $view->vars['accepted_files'] = $options['accepted_files'];
        $view->vars['limit'] = $options['limit'];
        $view->vars['url'] = $options['url'] ? $options['url'] : null;
        $view->vars['row_class'] = $options['row_class'];
        // var_dump($view->vars['value']);var_dump($options['value']);die;
        $view->vars['type'] = $options['type'];
        if($options['model_transformer'] !== 'Goteo\Util\Form\DataTransformer\ModelImageTransformer') {
            $view->vars['type'] = 'file';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'dropfiles';
    }
}
