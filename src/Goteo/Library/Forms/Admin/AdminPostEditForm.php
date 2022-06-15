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

use Goteo\Library\Forms\Model\ProjectPostForm;
use Goteo\Library\Text;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DatepickerType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\MediaType;
use Goteo\Util\Form\Type\TagsType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\TypeaheadType;
use Symfony\Component\Validator\Constraints;

class AdminPostEditForm extends ProjectPostForm {

    public function createForm() {
        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $post = $this->getModel();
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
            ->add('subtitle', TextType::class, array(
                'required' => false,
                'label' => 'admin-title-subtitle'
            ))
            ->add('date', DatepickerType::class, array(
                'label' => 'regular-date',
                'constraints' => array(new Constraints\NotBlank()),
            ))
            ->add('header_image', DropfilesType::class, array(
                'required' => false,
                'limit' => 1,
                'label' => 'admin-title-header-image',
                'accepted_files' => 'image/jpeg,image/gif,image/png'
            ))
            ->add('author', TypeaheadType::class, [
                'label' => 'regular-author',
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'sources' => 'user',
                'text' => ($post && $post->getAuthor()) ? $post->getAuthor()->name : null,
                'constraints' => array(new Constraints\NotBlank())
            ]);

        if($data['slug']) {
            $builder->add('slug', TextType::class,[
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
            ->add('type', ChoiceType::class, array(
                'label' => 'admin-text-type',
                'row_class' => 'extra',
                'choices' => [
                    Text::get('admin-text-type-md') => 'md',
                    Text::get('admin-text-type-html') => 'html'
                ],
                'attr' => [
                    'data-editor-type' => 'text',
                    'help' => Text::get('tooltip-text-type-change')
                ]
            ));

        $this->displayTextEditor($post->type, $builder);

        // Add tags input
        $tags = implode(',', array_keys($data['tags']));
        $jtags = array_map(
            function($k, $v) {
                return ['id' => $k, 'tag' => $v];
            },
            array_keys($data['tags']), $data['tags']
        );

        $builder
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
                    new Constraints\Count(array('max' => 20))
                )
            ))
            ->add('tags', TagsType::class, [
                'label' => 'admin-title-tags',
                'data' => $tags,
                'attr' => [
                    'data-item-value' => 'id', // id field for tagsinput
                    'data-item-text' => 'tag', // text field for tagsinput
                    'data-key-value' => 'id', // id field for bloodhound via api
                    'data-key-text' => 'tag', // text field for bloodhound via api
                    'data-limit' => 20, // total results in typeahead
                    'data-max-tags' => 3, // Max number of tags allowed
                    'data-min' => 0, // Shows immediately on focus the list if 0
                    // TODO: pass the template to show a table instead of a list
                    'data-values' => json_encode($jtags),
                    'autocomplete' => false
                ],
                'required' => false,
                'url' => '/api/blog/tags'
            ])
            ->add('media', MediaType::class, array(
                'label' => 'regular-media',
                'required' => false
            ))
            ->add('section', ChoiceType::class, array(
                'label' => 'admin-title-section',
                'expanded' => true,
                'wrap_class' => 'col-xs-6',
                'choices' => array_flip($post::getListSections()),
                'constraints' => [
                    new Constraints\NotBlank()
                ]
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
            ));

        return $this;
    }

    private function displayTextEditor(string $type, $builder)
    {
        if($type === 'html') {
            $builder->add('text', TextareaType::class, array(
                'label' => 'admin-title-text',
                'required' => false,
                'html_editor' => true,
                'attr' => [
                    'data-image-upload' => '/api/blog/images',
                    'help' => Text::get('tooltip-drag-and-drop-images')
                ]
            ));
        } else {
            $builder->add('text', MarkdownType::class, array(
                'label' => 'regular-text',
                'required' => false,
                'attr' => [
                    'data-image-upload' => '/api/blog/images',
                    'help' => Text::get('tooltip-drag-and-drop-images')
                ]
            ));
        }
    }

}
