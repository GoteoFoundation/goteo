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
                    new Constraints\Length([
                        'min' => 4,
                        'allowEmptyString' => true,
                    ]),
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

        $gallery = Image::getModelGallery('post', $post->id);
        $post->image = $gallery;

        if ($data['image'] && is_array($data['image'])) {
            if ($data['image']['removed']) {
                $removed_ids = array_column($data['image']['removed'], null, 'id');

                if(is_array($post->image)) {
                    foreach($post->image as $index => $img) {
                        if(in_array($img->id, $removed_ids)) {
                            $img->delFromModelGallery('post', $post->id);
                            unset($post->image[$index]);
                        }
                    }
                }
            }

            if ($data['image']['uploads']) {
                $post->image = array_merge($post->image, $data['image']['uploads']);
            }
        }

        $this->processImageChange($data['header_image'], $post->header_image, false);

        unset($data['image']);
        unset($data['header_image']);
        $post->rebuildData($data, array_keys($form->all()));

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
