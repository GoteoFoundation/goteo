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

use Goteo\Application\Currency;
use Goteo\Application\Lang;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Text;
use Goteo\Model\Project;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\Sdg;
use Goteo\Model\SocialCommitment;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\LocationType;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\MediaType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Symfony\Component\Validator\Constraints;
use Goteo\Util\Form\Type\UrlType;
use Goteo\Util\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\FormType;



class ProjectOverviewForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function getConstraints($field): array
    {
        $constraints = [];

        if ($field === 'name') {
            $constraints[] = new Constraints\NotBlank();
        } elseif ($field === 'subtitle') {
            $constraints[] = new Constraints\Length(['max' => 140]);
        } elseif ($field === 'description') {
            // Minimal 80 words
            $constraints[] = new Constraints\Regex([
                'pattern' => '/^\s*\S+(?:\s+\S+){79,}\s*$/',
                'message' => Text::get('validate-project-field-description')
            ]);
        }

        if ($this->getFullValidation()) {
            if(!in_array($field, ['media', 'spread'])) {
                $constraints[] = new Constraints\NotBlank();
            }
        }

        return $constraints;
    }

    public function createForm() {
        $model = $this->getModel();
        $project = $this->getModel();

        $builder = $this->getBuilder();
        $builder
            ->add('name', TextType::class, [
                'label' => 'overview-field-name',
                'constraints' => $this->getConstraints('name'),
                'disabled' => $this->getReadonly(),
                'attr' => ['help' => Text::get('tooltip-project-name')]
            ])
            ->add('subtitle', TextType::class, [
                'label' => 'overview-field-subtitle',
                'constraints' => $this->getConstraints('subtitle'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-subtitle')]
            ])
            ->add('project_location', LocationType::class, [
                'label' => 'overview-field-project_location',
                'constraints' => $this->getConstraints('project_location'),
                'type' => 'project', // API geoloc
                'item' => $this->getModel()->id,
                'disabled' => $this->getReadonly(),
                'location_object' => ProjectLocation::get($project),
                'location_class' => 'Goteo\Model\Project\ProjectLocation',
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>',
                'attr' => ['help' => Text::get('tooltip-project-project_location')]
            ])
            ->add('lang', ChoiceType::class, [
                'label' => 'overview-field-lang',
                'constraints' => $this->getConstraints('lang'),
                'disabled' => $this->getReadonly(),
                'choices' => $this->getLanguagesAsChoices(),
                'attr' => ['help' => Text::get('tooltip-project-lang')]
            ])
            ->add('currency', ChoiceType::class, [
                'label' => 'overview-field-currency',
                'constraints' => $this->getConstraints('currency'),
                'disabled' => $this->getReadonly(),
                'choices' => $this->getCurrenciesAsChoices(),
                'attr' => ['help' => Text::get('tooltip-project-currency')]
            ])
            ->add('media', MediaType::class, array(
                'label' => 'overview-field-media',
                'constraints' => $this->getConstraints('media'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-media')]
            ))
            ->add('description', MarkdownType::class, [
                'label' => 'overview-field-description',
                'constraints' => $this->getConstraints('description'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => [
                    'help' => Text::get('tooltip-project-description') .
                              '<br><i class="fa fa-hand-o-right" aria-hidden="true"></i> ' .
                              Text::get('tooltip-drag-and-drop-images'),
                    'rows' => 8,
                    'data-image-upload' => '/api/projects/' . $project->id . '/images'
                ]
            ])
            ->add('about', MarkdownType::class, [
                'label' => 'overview-field-about',
                'constraints' => $this->getConstraints('about'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => [
                    'help' => Text::get('tooltip-project-about') .
                              '<br><i class="fa fa-hand-o-right" aria-hidden="true"></i> ' .
                              Text::get('tooltip-drag-and-drop-images'),
                    'rows' => 8,
                    'data-image-upload' => '/api/projects/' . $project->id . '/images'
                ]
            ])
            ->add('motivation', MarkdownType::class, [
                'label' => 'overview-field-motivation',
                'constraints' => $this->getConstraints('motivation'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => [
                    'help' => Text::get('tooltip-project-motivation') .
                              '<br><i class="fa fa-hand-o-right" aria-hidden="true"></i> ' .
                              Text::get('tooltip-drag-and-drop-images'),
                    'rows' => 8,
                    'data-image-upload' => '/api/projects/' . $project->id . '/images'
                ]
            ])
            ->add('related', MarkdownType::class, [
                'label' => 'overview-field-related',
                'constraints' => $this->getConstraints('related'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => [
                    'help' => Text::get('tooltip-project-related') .
                              '<br><i class="fa fa-hand-o-right" aria-hidden="true"></i> ' .
                              Text::get('tooltip-drag-and-drop-images'),
                    'rows' => 8,
                    'data-image-upload' => '/api/projects/' . $project->id . '/images'
                ]
            ]);

        if($project->goal) {
            $builder
                ->add('goal', MarkdownType::class, [
                    'label' => 'overview-field-goal',
                    'disabled' => $this->getReadonly(),
                    'constraints' => $this->getConstraints('goal'),
                    'required' => false,
                    'attr' => ['help' => Text::get('tooltip-project-goal'), 'rows' => 8]
                ]);
        }

        $builder
            ->add('scope', ChoiceType::class, [
                'label' => 'overview-field-scope',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('choice'),
                'required' => false,
                'wrap_class' => 'col-sm-3 col-xs-4',
                'choices' => $this->getProjectScopesAsChoices(),
                'expanded' => true,
                'attr' => ['help' => Text::get('tooltip-project-scope')]
            ])
            ->add('social_commitment', ChoiceType::class, [
                'label' => 'overview-field-social-category',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('social_commitment'),
                'required' => false,
                'choices' => $this->getSocialCommitmentsAsChoices(),
                'expanded' => true,
                'attr' => ['help' => Text::get('tooltip-project-social-category')]
            ])
            ->add('sdgs', ChoiceType::class, [
                'label' => 'admin-title-sdgs',
                'data' => array_column($model->getSdgs(), 'id'),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'choices' => $this->getSdgsChoices(),
                'choices_label_escape' => false,
                'wrap_class' => 'col-md-2 col-sm-4 col-xs-4 col-xxs-6',
                'attr' => [
                    'pre-help' => $this->getProjectGlobalSocialCommitments($project),
                    'help' => Text::get('tooltip-project-sdg'),
                    'label_class'=> 'center-block'
                ]
            ])
            ->add('social_commitment_description', TextareaType::class, [
                'disabled' => $this->getReadonly(),
                'label' => 'overview-field-social-description',
                'constraints' => $this->getConstraints('social_commitment_description'),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-social-description'), 'rows' => 8]
            ])

            ->add('sign_check', BooleanType::class, [
                'label' => 'overview-field-sign-check', // 'overview-field-sign-check',
                'required' => false,
                'data' => $model->sign_url && $model->sign_url_action ? true : false,
                'mapped' => false
            ])

            ->add('sign_url', UrlType::Class, [
                'label' => 'overview-field-sign-url',
                'required' => false,
                'data' => $model->sign_url,
                'row_class' => $model->sign_url && $model->sign_url_action ? '' : 'hidden',
                'attr' => [
                    'help' => Text::get('overview-field-sign-url-help')
                ]
            ])

            ->add('sign_url_action', TextType::class, [
                'label' => 'overview-field-sign-url-action',
                'required' => false,
                'data' => $model->sign_url_action,
                'row_class' => $model->sign_url && $model->sign_url_action ? '' : 'hidden'
            ])
            ;

        return $this;
    }

    private function getLanguagesAsChoices(): array
    {
        $choices = [];
        $languages = Lang::listAll('name', false);

        foreach ($languages as $key => $value) {
            $choices[$value] = $key;
        }

        return $choices;
    }

    private function getCurrenciesAsChoices(): array
    {
        $choices = [];
        $currencies = Currency::listAll('name');

        foreach ($currencies as $key => $value) {
            $choices[$value] = $key;
        }

        return $choices;
    }

    private function getSocialCommitmentsAsChoices(): array
    {
        $socialCommitments = SocialCommitment::getAll();

        return array_map(function($el) {
            return [$el->name => $el->id];
        }, $socialCommitments);
    }

    private function getProjectScopesAsChoices(): array
    {
        $choices = [];
        $projectScopes = Project::scope();

        foreach ($projectScopes as $key => $value) {
            $choices[$value] = $key;
        }

        return $choices;
    }

    private function getSdgsChoices(): array
    {
        $sdgs = Sdg::getList([],0,100);
        $choices = [];

        foreach($sdgs as $s) {
            $choices['<img style="display: block; margin: 0 auto;" src="'.$s->getIcon()->getLink().'" class="icon icon-5x"><span style="text-align: center; display: block;">'.$s->name.'</span>']
                = $s->id;
        }

        return $choices;
    }

    private function getProjectGlobalSocialCommitments($project): string
    {
        $social_commitment = $project->getSocialCommitment();

        if ($social_commitment) {
            foreach($social_commitment->getSdgs() as $s) {
                $sdgs_suggestion.='<img data-toggle="tooltip" title="'.$s->name .'" data-placement="bottom" data-value="'.$s->id.'" display="block;" src="'.$s->getIcon()->getLink().'" class="icon icon-6x clickable" style="margin-left: 5px;"> ';
            }

            return '<span id="sdgs_suggestion_label" style="font-size: 14px;">'.Text::get('tooltip-project-sdg-suggestion'). '</span><span id="sdgs_suggestion" class="center-block">'.$sdgs_suggestion.'</span><hr style="margin-top: 30px; margin-bottom: 30px; border: 2px solid #FFF;">';
        } else {
            return '<span id="sdgs_suggestion_label" style="display: none; font-size: 14px;">'.Text::get('tooltip-project-sdg-suggestion').'</span><span id="sdgs_suggestion" class="center-block"></span><hr style="margin-top: 30px; margin-bottom: 30px; border: 2px solid #FFF;">';
        }
    }

}
