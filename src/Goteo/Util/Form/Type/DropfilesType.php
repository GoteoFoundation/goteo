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

use Goteo\Model\Contract\BaseDocument;
use Goteo\Model\Contract\Document;
use Goteo\Model\Image;
use Goteo\Library\Text;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;

use Goteo\Application\Exception\ModelNotFoundException;

/**
 *
 * This class creates a Symfony Form Type uploading files using Dropzone (needs assets/js/forms.js)
 *
 */
class DropfilesType extends FileType
{

    const TYPE_DOCUMENT = 'document';
    const TYPE_CONTRACT = 'contract';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addCurrentViewAndModelTransformer($options, $builder);
        $this->addUploadsViewFileType($builder);
        $this->addUploadsFieldModelTransformer($options, $builder);
        $this->addRemovedViewCollectionType($builder);
        $this->addRemovedFieldModelTransformer($options, $builder);
        $this->addViewTransformer($options, $builder);
    }

    private function addCurrentViewAndModelTransformer(
        array $options,
        FormBuilderInterface $builder
    ) {
        $builder->add('current', FileType::class, [
            'multiple' => true,
            'data' => is_array($options['data']) ? $options['data'] : [$options['data']],
            'data_class' => null
        ]);

        $builder->get('current')
            ->addModelTransformer(new $options['model_transformer']);
    }

    private function addUploadsViewFileType(FormBuilderInterface $builder)
    {
        $builder->add('uploads', FileType::class, [
            'multiple' => true
        ]);
    }

    private function addUploadsFieldModelTransformer(
        array $options,
        FormBuilderInterface $builder
    ) {
        if ($options['type'] == DropfilesType::TYPE_DOCUMENT) {
            $this->addUploadsTransformerAsDocument($builder);
        } else if ($options['type'] == DropfilesType::TYPE_CONTRACT) {
            $this->addUploadsTransformerAsContractDocument($builder);
        } else {
            $builder->get('uploads')
                ->addModelTransformer(new $options['upload_transformer']);
        }
    }

    private function addUploadsTransformerAsDocument(FormBuilderInterface $builder)
    {
        $builder->get('uploads')
            ->addModelTransformer(new CallbackTransformer(
                function($image) {
                    return null;
                },
                function($image) {
                    if(is_array($image)) {
                        foreach($image as $i => $img) {
                            if(!$img) continue;

                            if(!$img instanceOf BaseDocument) {
                                $image[$i] = new BaseDocument($img);
                                $image[$i]->save();
                            }
                        }
                    }
                    return $image;
                }
            ));
    }

    private function addUploadsTransformerAsContractDocument(FormBuilderInterface $builder)
    {
        $builder->get('uploads')
            ->addModelTransformer(new CallbackTransformer(
                function($image) {
                    return null;
                },
                function($image) {
                    if(is_array($image)) {
                        foreach($image as $i => $img) {
                            if(!$img) continue;

                            if(!$img instanceOf Document) {
                                $image[$i] = new Document($img);
                                $image[$i]->save();
                            }
                        }
                    }
                    return $image;
                }
            ));
    }


    private function addRemovedViewCollectionType(FormBuilderInterface $builder)
    {
        $builder->add('removed', CollectionType::class, [
            'entry_type' => TextType::class,
            'allow_add' => true
        ]);
    }

    private function addRemovedFieldModelTransformer(
        array $options,
        FormBuilderInterface $builder
    ) {
        $builder->get('removed')
            ->addModelTransformer( new CallbackTransformer(
                function($image) {
                    return $image;
                },
                function($image) use($options) {
                    $images = [];
                    foreach($image as  $img) {
                        if (!$img)
                            continue;
                        if ($options['type'] == DropfilesType::TYPE_DOCUMENT && $img = BaseDocument::getByName($img))
                            $images[] = $img;
                        else if ($options['type'] == DropfilesType::TYPE_CONTRACT && $img = Document::getByName($img))
                            $images[] = $img;
                        else if ($img = Image::get($img))
                            $images[] = $img;
                    }

                    return $images;
                }
            ));
    }

    private function addViewTransformer(
        array $options,
        FormBuilderInterface $builder
    ) {
        $builder->addViewTransformer(new CallbackTransformer(
            function($image) use ($options) {
                if ($image instanceOf File)
                    if ($options['type'] == 'document')
                        $image = new BaseDocument($image);
                    else
                        $image = new Image($image);
                if (is_array($image)) {
                    foreach($image as $i => $img) {
                        if ($image instanceOf File)
                            if ($options['type'] == 'document')
                                $image = new BaseDocument($image);
                            else
                                $image = new Image($image);
                    }
                }

                return is_array($image) ? $image : [$image];
            },
            function ($image) {
                $img = isset($image['current']) && is_array($image['current']) ? $image['current'] : [];
                if($image['uploads']) {
                    if(is_array($image['uploads'])) {
                        $img = array_merge($img, ['uploads' => $image['uploads']]);
                    }
                }
                if ($image['removed']) {
                    $img = array_merge($img, ['removed' => $image['removed']]   );
                }
                return $img;
            })
        );
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
        if(!is_array($view->vars['data'])) {
            $options['limit'] = 1;
        }
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
