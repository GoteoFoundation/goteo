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
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Application\Lang;
use Goteo\Model\Project;
use Goteo\Model\SocialCommitment;
use Goteo\Library\Text;
use Goteo\Application\Currency;
use Goteo\Library\Forms\FormModelException;
use Goteo\Model\Project\ProjectLocation;

class ProjectOverviewForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function getConstraints($field) {
        $constraints = [];
        if($field === 'name') {
            $constraints[] = new Constraints\NotBlank();
        }
        if($field === 'subtitle') {
            $constraints[] = new Constraints\Length(['max' => 140]);
        }
        if($field === 'description') {
            // Minimal 80 words
            $constraints[] = new Constraints\Regex([
                'pattern' => '/^\s*\S+(?:\s+\S+){79,}\s*$/',
                'message' => Text::get('validate-project-field-description')
            ]);
        }
        if($this->getFullValidation()) {
            if(!in_array($field, ['media', 'spread'])) {
                // all fields
                $constraints[] = new Constraints\NotBlank();
            }
        }
        return $constraints;
    }

    public function createForm() {
        $currencies = Currency::listAll('name', false);
        $langs = Lang::listAll('name', false);
        $project = $this->getModel();
        $builder = $this->getBuilder();
        $builder
            ->add('name', 'text', [
                'label' => 'overview-field-name',
                'constraints' => $this->getConstraints('name'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-project-name')]
            ])
            ->add('subtitle', 'text', [
                'label' => 'overview-field-subtitle',
                'constraints' => $this->getConstraints('subtitle'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-subtitle')]
            ])
            ->add('project_location', 'location', [
                'label' => 'overview-field-project_location',
                'constraints' => $this->getConstraints('project_location'),
                // 'type' => 'project', // JSON geoloc
                // 'item' => $this->getModel()->id,
                'disabled' => $this->getReadonly(),
                'location_object' => ProjectLocation::get($project),
                'location_class' => 'Goteo\Model\Project\ProjectLocation',
                // 'location_radius' => 10,
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>',
                'attr' => ['help' => Text::get('tooltip-project-project_location')]
            ])
            ->add('lang', 'choice', [
                'label' => 'overview-field-lang',
                'constraints' => $this->getConstraints('lang'),
                'disabled' => $this->getReadonly(),
                'choices' => $langs,
                'attr' => ['help' => Text::get('tooltip-project-lang')]
            ])
            ->add('currency', 'choice', [
                'label' => 'overview-field-currency',
                'constraints' => $this->getConstraints('currency'),
                'disabled' => $this->getReadonly(),
                'choices' => $currencies,
                'attr' => ['help' => Text::get('tooltip-project-currency')]
            ])
            ->add('media', 'media', array(
                'label' => 'overview-field-media',
                'constraints' => $this->getConstraints('media'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-media')]
            ))
            ->add('description', 'markdown', [
                'label' => 'overview-field-description',
                'constraints' => $this->getConstraints('description'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-description'), 'rows' => 8]
            ])
            ->add('about', 'markdown', [
                'label' => 'overview-field-about',
                'constraints' => $this->getConstraints('about'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                // 'row_class' => 'extra',
                'attr' => ['help' => Text::get('tooltip-project-about'), 'rows' => 8]
            ])
            ->add('motivation', 'markdown', [
                'label' => 'overview-field-motivation',
                'constraints' => $this->getConstraints('motivation'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                // 'row_class' => 'extra',
                'attr' => ['help' => Text::get('tooltip-project-motivation'), 'rows' => 8]
            ])
            ->add('related', 'markdown', [
                'label' => 'overview-field-related',
                'constraints' => $this->getConstraints('related'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-related'), 'rows' => 8]
            ])
            // ->add('spread', 'textarea', [
            //     'label' => 'overview-field-spread',
            //     'disabled' => $this->getReadonly(),
            //     'constraints' => $this->getConstraints('spread'),
            //     'required' => false,
            //     'attr' => ['help' => Text::get('tooltip-project-spread'), 'info' => '<i class="fa fa-eye-slash"></i> '. Text::get('project-non-public-field'), 'rows' => 8]
            // ])
            ;

            // ->add('extra-title', 'title', [
            //     'label' => 'overview-extra-fields',
            //     'disabled' => $this->getReadonly(),
            //     'row_class' => 'extra'
            // ])
        if($project->goal) {
            $builder
                ->add('goal', 'markdown', [
                    'label' => 'overview-field-goal',
                    'disabled' => $this->getReadonly(),
                    'constraints' => $this->getConstraints('goal'),
                    'required' => false,
                    // 'row_class' => 'extra',
                    'attr' => ['help' => Text::get('tooltip-project-goal'), 'rows' => 8]
                ]);
        }
        $builder
            ->add('scope', 'choice', [
                'label' => 'overview-field-scope',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('choice'),
                'required' => false,
                'wrap_class' => 'col-sm-3 col-xs-4',
                'choices' => Project::scope(),
                'expanded' => true,
                'attr' => ['help' => Text::get('tooltip-project-scope')]
            ])
            ->add('social_commitment', 'choice', [
                'label' => 'overview-field-social-category',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('social_commitment'),
                'required' => false,
                // 'wrap_class' => 'col-sm-3 col-xs-4',
                'choices' => array_map(function($el){
                        return [$el->id => $el->name];
                    }, SocialCommitment::getAll()),
                'expanded' => true,
                'attr' => ['help' => Text::get('tooltip-project-social-category')]
            ])
            ->add('social_commitment_description', 'textarea', [
                'disabled' => $this->getReadonly(),
                'label' => 'overview-field-social-description',
                'constraints' => $this->getConstraints('social_commitment_description'),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-social-description'), 'rows' => 8]
            ])
            ;
        return $this;
    }

}
