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

use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\MediaType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;
use Goteo\Util\Form\Type\UrlType;

class ProjectTranslateOverviewForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function createForm() {
        $project = $this->getModel();

        $builder = $this->getBuilder()
            ->add('name', TextType::class, [
                'label' => 'overview-field-name',
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => $project->name]
            ])
            ->add('subtitle', TextType::class, [
                'label' => 'overview-field-subtitle',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => $project->subtitle]
            ])
            ->add('description', MarkdownType::class, [
                'label' => 'overview-field-description',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => [
                    'help' => $project->description,
                    'rows' => 8,
                    'data-image-upload' => '/api/projects/' . $project->id . '/images'
                ]
            ])
            ->add('media', MediaType::class, [
                'label' => 'overview-field-media',
                'disabled' => $this->getReadonly(),
                'required' => false,

              'attr' => ['help' => $project->media]
            ])
            ->add('motivation', MarkdownType::class, [
                'label' => 'overview-field-motivation',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => $project->motivation, 'rows' => 8]
            ])
            ->add('about', MarkdownType::class, [
                'label' => 'overview-field-about',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => $project->about, 'rows' => 8]
            ]);
        if($project->goal) {
            $builder
                ->add('goal', MarkdownType::class, [
                    'label' => 'overview-field-goal',
                    'disabled' => $this->getReadonly(),
                    'required' => false,
                    'attr' => ['help' => $project->goal, 'rows' => 8]
                ]);
        }

        $builder
            ->add('related', MarkdownType::class, [
                'label' => 'overview-field-related',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => $project->related, 'rows' => 8]
            ])
            ->add('social_commitment_description', TextareaType::class, [
                'label' => 'overview-field-social-description',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => $project->social_commitment_description, 'rows' => 8]
            ])
            ->add('sign_url', UrlType::Class, [
                'label' => 'overview-field-sign-url',
                'required' => false,
                'attr' => [
                    'pre-help' => Text::get('overview-field-sign-url-help')
                ]
            ])

            ->add('sign_url_action', TextType::class, [
                'label' => 'overview-field-sign-url-action',
                'required' => false
            ])
            ;
        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $project = $this->getModel();
        $lang = $this->getOption('lang');

        $data = array_intersect_key($form->getData(), $form->all());
        $errors = [];
        $project->lang = $lang;
        $data['keywords'] = $project->keywords; // Do not translate keywords for the moment
        if(!$project->setLang($lang, $data, $errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(',',$errors)));
        }
        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));
        return $this;
    }
}
