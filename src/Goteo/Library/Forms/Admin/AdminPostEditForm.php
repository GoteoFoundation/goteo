<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\Forms\Admin;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\User;
use Goteo\Library\Forms\Model\ProjectPostForm;
use Goteo\Library\Forms\FormModelException;

class AdminPostEditForm extends ProjectPostForm {

    public function createForm() {
        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $post = $this->getModel();
        $data = $options['data'];

        $builder
            ->add('title', 'text', array(
                'label' => 'regular-title',
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 4)),
                ),
            ))
            ->add('subtitle', 'text', array(
                'required' => false,
                'label' => 'admin-title-subtitle'
            ))
            ->add('date', 'datepicker', array(
                'label' => 'regular-date',
                'constraints' => array(new Constraints\NotBlank()),
            ))
            ->add('header_image', 'dropfiles', array(
                'required' => false,
                'limit' => 1,
                'label' => 'admin-title-header-image',
                'accepted_files' => 'image/jpeg,image/gif,image/png'
            ))
            ->add('author', 'typeahead', [
                'label' => 'regular-author',
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'sources' => 'user',
                'text' => ($post && $post->getAuthor()) ? $post->getAuthor()->name : null,
                'constraints' => array(new Constraints\NotBlank())
            ])
;

        if($data['slug']) {
            $builder->add('slug', 'text',[
                'label' => 'regular-slug',
                'row_class' => 'extra',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Callback(function($object, $context) use ($post){
                        if($object != $post::idealiza($object, false, false, 150)) {
                            $context->buildViolation(Text::get('admin-blog-slug-chars', $object))
                            ->atPath('slug')
                            ->addViolation();
                        }

                        if($post->slugExists($object)) {
                            $context->buildViolation(Text::get('admin-blog-slug-exists', $object))
                            ->atPath('slug')
                            ->addViolation();
                        }
                    })
                ],
                'attr' => [
                    'help' => Text::get('admin-slug-seo-help')
                ]
            ]);
        }
        $builder
            ->add('type', 'choice', array(
                'label' => 'admin-text-type',
                'row_class' => 'extra',
                'choices' => ['md' => Text::get('admin-text-type-md'), 'html' => Text::get('admin-text-type-html')],
                'attr' => [
                    'data-editor-type' => 'text',
                    'help' => Text::get('tooltip-text-type-change')
                ]
            ));

        // Replace markdown by html editor if type
        if($post->type === 'html') {
            $builder->add('text', 'textarea', array(
                'label' => 'admin-title-text',
                'required' => false,
                'html_editor' => true,
                // 'constraints' => array(new Constraints\NotBlank()),
                'attr' => [
                    'data-image-upload' => '/api/blog/images',
                    'help' => Text::get('tooltip-drag-and-drop-images')
                ]
            ));
        } else {
            $builder->add('text', 'markdown', array(
                'label' => 'regular-text',
                'required' => false,
                // 'constraints' => array(new Constraints\NotBlank()),
                'attr' => [
                    'data-image-upload' => '/api/blog/images',
                    'help' => Text::get('tooltip-drag-and-drop-images')
                ]
            ));
        }

        // Add tags input
        $tags = implode(',', array_keys($data['tags']));
        $jtags = array_map(function($k, $v) {
                return ['id' => $k,'tag' => $v];
            }, array_keys($data['tags']), $data['tags']);

        $builder
            // saving images will add that images to the gallery
            // let's show the gallery in the field with nice options
            // for removing and reorder it
            ->add('image', 'dropfiles', array(
                'required' => false,
                'data' => $data['gallery'],
                'label' => 'regular-images',
                'markdown_link' => 'text',
                'accepted_files' => 'image/jpeg,image/gif,image/png',
                'constraints' => array(
                    new Constraints\Count(array('max' => 20))
                )
            ))
            ->add('tags', 'tags', [
                'label' => 'admin-title-tags',
                'data' => $tags,
                'attr' => [
                    'data-item-value' => 'id', // id field for tagsinput
                    'data-item-text' => 'tag', // text field for tagsinput
                    'data-key-value' => 'id', // id field for bloodhound via api
                    'data-key-text' => 'tag', // text field for bloodhound via api
                    'data-limit' => 20, // total results in typeahead
                    'data-max-tags' => 3, // Max number of tags allowed
                    'data-min' => 0, // Shows inmediatly on focus the list if 0
                    // TODO: pass the template to show a table instead of a list
                    'data-values' => json_encode($jtags),
                    'autocomplete' => false
                ],
                'required' => false,
                'url' => '/api/blog/tags'
            ])
            ->add('media', 'media', array(
                'label' => 'regular-media',
                'required' => false
            ))
            ->add('section', 'choice', array(
                'label' => 'admin-title-section',
                'expanded' => true,
                'wrap_class' => 'col-xs-6',
                'choices' => $post::getListSections(),
                'constraints' => [
                    new Constraints\NotBlank()
                ]
            ))
            ->add('allow', 'boolean', array(
                'required' => false,
                'label' => 'blog-allow-comments', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ->add('publish', 'boolean', array(
                'required' => false,
                'label' => 'blog-published', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ;

        return $this;
    }

}
