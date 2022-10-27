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
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Filter\FilterLocation;
use Goteo\Model\Invest;
use Goteo\Model\Project;
use Goteo\Model\SocialCommitment;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DatepickerType;
use Goteo\Util\Form\Type\LocationType;
use Goteo\Util\Form\Type\MultipleTypeaheadType;
use Goteo\Util\Form\Type\NumberType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\TypeaheadType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;


class FilterForm extends AbstractFormProcessor {

    public function createForm(): FilterForm
    {
        $model = $this->getModel();
        $builder = $this->getBuilder();

        $builder
            ->add('name', TextType::class, [
                'label' => 'regular-title',
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => '',
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(),
                ]
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'admin-filter-typeofuser',
                'choices' => $this->getRolesChoices(),
                'required' => true,
            ])
            ->add('predefineddata', ChoiceType::class, [
                'label' => 'admin-filter-predefined-date',
                'required' => false,
                'choices' => $this->getAntiquityChoices(),
                'mapped' => false
            ])
            ->add('startdate', DatepickerType::class, [
                'label' => 'regular-date_in',
                'required' => false,
            ])
            ->add('enddate', DatepickerType::class, [
                'label' => 'regular-date_out',
                'required' => false,
            ])
            ->add('projects', MultipleTypeaheadType::class, [
                'label' => 'admin-projects',
                'value_field' => 'name',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'sources' => 'project',
            ])
            ->add('calls', MultipleTypeaheadType::class, [
                'label' => 'admin-calls',
                'value_field' => 'name',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'sources' => 'call',
            ])
            ->add('channels', MultipleTypeaheadType::class, [
                'label' => 'admin-channels',
                'value_field' => 'name',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'sources' => 'channel',
            ])
            ->add('matchers', MultipleTypeaheadType::class, [
                'label' => 'admin-matchers',
                'value_field' => 'name',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'sources' => 'matcher',
            ])
            ->add('social_commitments', ChoiceType::class,[
                'label' => 'regular-social_commitments',
                'required' => false,
                'choices' => $this->getSocialCommitmentsAsChoices(),
                'data' => array_keys($this->getSocialCommitmentsData()),
                'expanded' => true,
                'multiple' => true,
                'attr' => ['help' => Text::get('tooltip-project-social-category')]
            ])
            ->add('project_status', ChoiceType::class, [
                'label' => 'admin-filter-project-status',
                'required' => false,
                'choices' => $this->getAssociativeArrayChoices(Project::status()),
            ])
            ->add('invest_status', ChoiceType::class, [
                'label' => 'admin-filter-invest-status',
                'required' => false,
                'choices' => $this->getAssociativeArrayChoices(Invest::status()),
            ])
            ->add('amount', NumberType::class, [
                'label' => 'regular-amount',
                'required' => false
            ])
            ->add('donor_status', ChoiceType::class, [
                'label' => 'admin-filter-donor-status',
                'required' => false,
                'choices' => $this->getDonorStatusChoices()
            ])
            ->add('typeofdonor', ChoiceType::class, [
                'label' => 'admin-filter-typeofdonor',
                'required' => false,
                'choices' => $this->getTypeOfDonorChoices(),
            ])
            ->add('foundationdonor', ChoiceType::class, [
                'required' => false,
                'label' => 'admin-filter-type-foundation-donor',
                'choices' => $this->getNoYesChoices()
            ])
            ->add('wallet', ChoiceType::class, [
                'required' => false,
                'label' => Text::get('admin-user-wallet-amount'),
                'choices' => $this->getNoYesChoices()
            ])
            ->add('cert', ChoiceType::class, [
                'required' => false,
                'label' => Text::get('home-advantages-certificates-title'),
                'choices' => $this->getNoYesChoices()
            ])
            ->add('filter_location', LocationType::class, [
                'label' => 'admin-filter-location',
                'disabled' => $this->getReadonly(),
                'location_object' => FilterLocation::get($model),
                'location_class' => 'Goteo\Model\Filter\FilterLocation',
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>'
            ])
            ->add('forced', BooleanType::class, [
                'label' => 'admin-filter-forced',
                'required' => false,
                'attr' => [
                    'pre-help' => Text::get('admin-filter-forced-help')
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'regular-submit',
                'attr' => ['class' => 'btn btn-cyan'],
                'icon_class' => 'fa fa-save'
            ]);

        return $this;
    }

    private function getAssociativeArrayChoices($associativeArray): array
    {
        $choices = [];

        foreach ($associativeArray as $k => $v) {
            $choices[$v] = $k;
        }

        return $choices;
    }

    private function getAntiquityChoices(): array
    {
        return [
            Text::get('admin-filter-last-week') => 0,
            Text::get('admin-filter-last-month') => 1,
            Text::get('admin-filter-last-year') => 2,
            Text::get('admin-filter-from-new-year') => 3,
            Text::get('admin-filter-previous-year') => 4,
            Text::get('admin-filter-two-years-ago') => 5
        ];
    }

    private function getRolesChoices(): array
    {
        return [
            Text::get('admin-filter-user') => 'user',
            Text::get('admin-filter-donor') => 'donor',
            Text::get('admin-filter-no-donor') => 'no-donor',
            Text::get('admin-filter-promoter') => 'promoter',
            Text::get('admin-filter-matcher') => 'matcher',
            Text::get('admin-filter-test') => 'test'
        ];
    }

    private function getDonorStatusChoices(): array
    {
        return [
            Text::get('donor-status-pending') => 'pending',
            Text::get('donor-status-completed') => 'completed',
            Text::get('donor-status-processed') => 'processed',
            Text::get('donor-status-validated') => 'validated',
            Text::get('donor-status-declared') => 'declared',
            Text::get('donor-status-rejected') => 'rejected'
        ];
    }

    private function getTypeOfDonorChoices(): array
    {
        return [
            Text::get('admin-filter-type-unique') => 'unique',
            Text::get('admin-filter-type-multidonor') => 'multidonor',
        ];
    }

    private function getNoYesChoices(): array
    {
        return [
            Text::get('admin-no') => 0,
            Text::get('admin-yes') => 1
        ];
    }

    private function getSocialCommitmentsAsChoices(): array
    {
        $socialCommitments = SocialCommitment::getAll();

        return array_map(function($el) {
            return [$el->name => $el->id];
        }, $socialCommitments);
    }

    private function getSocialCommitmentsData(): array
    {
        return array_map(function($el) {
            return $el->id;
        }, $this->getModel()->social_commitments);
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();
        $model->rebuildData($data, array_keys($form->all()));

        $model->projects = [];
        $model->calls = [];
        $model->channels = [];
        $model->matchers = [];
        $model->footprints = [];
        $model->sdgs = [];
        $model->social_commitments = [];

        foreach($data['projects'] as $value) {
            if (!empty($value)) array_push($model->projects, $value);
        }
        foreach($data['calls'] as $value) {
            if (!empty($value)) array_push($model->calls, $value);
        }
        foreach($data['channels'] as $value) {
            if (!empty($value)) array_push($model->channels, $value);
        }
        foreach($data['matchers'] as $value) {
            if (!empty($value)) array_push($model->matchers, $value);
        }
        foreach($data['sdgs'] as $value) {
            if (!empty($value)) array_push($model->sdgs, $value);
        }
        foreach($data['footprints'] as $value) {
            if (!empty($value)) array_push($model->footprints, $value);
        }
        foreach($data['social_commitments'] as $value) {
            if (!empty($value)) array_push($model->social_commitments, $value);
        }

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
