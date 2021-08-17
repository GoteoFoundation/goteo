<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\Forms\Model;

use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\DatepickerType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\MediaType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\Image;
use Goteo\Application\Session;
use Goteo\Library\Forms\FormModelException;
use Symfony\Component\Form\FormInterface;

class ProjectPostForm extends AbstractFormProcessor {

    public function createForm() {
        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $data = $options['data'];

        $builder
            ->add('title', TextType::class, array(
                'label' => 'regular-title',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 4)),
                ),
            ))
            ->add('date', DatepickerType::class, array(
                'label' => 'regular-date',
                'constraints' => array(new Constraints\NotBlank()),
            ))
            // saving images will add that images to the gallery
            // let's show the gallery in the field with nice options
            // for removing and reorder it
            ->add('image', DropfilesType::class, array(
                'required' => false,
                'data' => $data['gallery'],
                'label' => 'regular-images',
                'markdown_link' => 'text',
                'accepted_files' => 'image/jpeg,image/gif,image/png',
                'url' => '/api/projects/' . $this->getOption('project')->id . '/images',
                'constraints' => array(
                    new Constraints\Count(array('max' => 10)),
                    new Constraints\All(array(
                        'constraints' => array(
                            // new Constraints\File()
                            // new NotNull(array('groups'=>'Test'))
                        )
                    ))
                )
            ))
            ->add('text', MarkdownType::class, array(
                'label' => 'regular-text',
                'required' => false,
                'attr'=> [
                    'data-image-upload' => '/api/projects/' . $this->getOption('project')->id . '/images',
                    'help' => Text::get('tooltip-drag-and-drop-images')
                ]
            ))
            ->add('media', MediaType::class, array(
                'label' => 'regular-media',
                'required' => false
            ))
            ->add('allow', BooleanType::class, array(
                'required' => false,
                'label' => 'blog-allow-comments', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ->add('publish', BooleanType::class, array(
                'required' => false,
                'label' => 'blog-published', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ;
        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        if(array_key_exists('tags', $data)) $data['tags'] = explode(',', $data['tags']);
        $post = $this->getModel();
        $post->rebuildData($data, array_keys($form->all()));
        $post->image = $data['image'];
        if(is_array($data['header_image'])) $post->header_image = $data['header_image'][0];


        $gallery = Image::getModelGallery('post', $post->id);

        $current = array_map(function($e) {
                return is_object($e) ? $e->id : $e['id'];
            }, $post->image);

        if(is_array($gallery)) {
            foreach($gallery as $img) {
                if(!in_array($img->id, $current)) {
                    Image::deleteModelImage('post', $post->id);
                    $img->delFromModelGallery('post', $post->id);
                }
            }
        }

        if(!Session::getUser()->hasPerm('full-html-edit')) {
            $post->text = Text::tags_filter($post->text);
            $post->text = $post::sanitizeText($post->text);
        }

        $errors = [];
        if (!$post->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
