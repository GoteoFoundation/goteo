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
use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Application\Lang;
use Goteo\Model\Project;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;

class ProjectTranslateOverviewForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function createForm() {
        $project = $this->getModel();

        $this->getBuilder()
            ->add('subtitle', 'text', [
                'label' => 'overview-field-subtitle',
                'required' => false,
                'attr' => ['help' => $project->subtitle]
            ])
            ->add('description', 'markdown', [
                'label' => 'overview-field-description',
                'required' => false,
                'attr' => ['help' => $project->description, 'rows' => 8]
            ])
            ->add('media', 'media', [
                'label' => 'overview-field-media',
                'required' => false,
                'attr' => ['help' => $project->media]
            ])
            ->add('motivation', 'markdown', [
                'label' => 'overview-field-motivation',
                'required' => false,
                'attr' => ['help' => $project->motivation, 'rows' => 8]
            ])
            ->add('video', 'media', [
                'label' => 'overview-field-video',
                'required' => false,
                'attr' => ['help' => $project->video]
            ])
            ->add('about', 'markdown', [
                'label' => 'overview-field-about',
                'required' => false,
                'attr' => ['help' => $project->about, 'rows' => 8]
            ])
            ->add('goal', 'markdown', [
                'label' => 'overview-field-goal',
                'required' => false,
                'attr' => ['help' => $project->goal, 'rows' => 8]
            ])
            ->add('related', 'markdown', [
                'label' => 'overview-field-related',
                'required' => false,
                'attr' => ['help' => $project->related, 'rows' => 8]
            ])
            // ->add('keywords', 'tags', [
            //     'label' => 'overview-field-keywords',
            //     'required' => false,
            //     'attr' => ['help' => $project->keywords]
            // ])
            ->add('social_commitment_description', 'textarea', [
                'label' => 'overview-field-social-description',
                'required' => false,
                'attr' => ['help' => $project->social_commitment_description, 'rows' => 8]
            ])
            ;
        return $this;
    }

    public function save(FormInterface $form = null) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        $languages = Lang::listAll('name', false);
        $project = $this->getModel();
        $lang = $this->getOption('lang');

        $data = $form->getData();
        $errors = [];
        $project->lang = $lang;

        $data['keywords'] = $project->keywords; // Do not translate keywords for the moment
        if(!$project->setLang($lang, $data, $errors)) {
            // throw new FormModelException(Text::get('form-sent-error', implode(',',array_map('implode', $errors))));
            throw new FormModelException(Text::get('form-sent-error', implode(',',$errors)));
        }
        return $this;
    }
}
