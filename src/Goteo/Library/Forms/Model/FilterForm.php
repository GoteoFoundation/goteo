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
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DatepickerType;
use Goteo\Util\Form\Type\LocationType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\TypeaheadType;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;
use Symfony\Component\Form\FormInterface;
use Goteo\Model\Project;
use Goteo\Model\Sdg;
use Goteo\Model\Footprint;
use Goteo\Model\Invest;
// use Goteo\Model\User\DonorLocation;
// use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\Filter\FilterLocation;

class FilterForm extends AbstractFormProcessor {

    public function createForm() {

        $model = $this->getModel();
        $builder = $this->getBuilder();

        $antiquity = [
            '0' => Text::get('admin-filter-last-week'),
            '1' => Text::get('admin-filter-last-month'),
            '2' => Text::get('admin-filter-last-year'),
            '3' => Text::get('admin-filter-from-new-year'),
            '4' => Text::get('admin-filter-previous-year'),
            '5' => Text::get('admin-filter-two-years-ago')
        ];

        $roles = [
            'user' => Text::get('admin-filter-user'),
            'donor' => Text::get('admin-filter-donor'),
            'no-donor' => Text::get('admin-filter-no-donor'),
            'promoter' => Text::get('admin-filter-promoter') ,
            'matcher' => Text::get('admin-filter-matcher'),
            'test' => Text::get('admin-filter-test')
        ];

        $typeofdonor = [
            'unique' => Text::get('admin-filter-type-unique'),
            'multidonor' => Text::get('admin-filter-type-multidonor'),
        ];

        $donor_status = [
            'pending' => Text::get('donor-status-pending'),
            'completed' => Text::get('donor-status-completed'),
            'processed' => Text::get('donor-status-processed'),
            'validated' => Text::get('donor-status-validated'),
            'declared' => Text::get('donor-status-declared'),
            'rejected' => Text::get('donor-status-rejected')
        ];

        $builder
            ->add('name', TextType::class, array(
                'label' => 'regular-title',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                ),
            ))
            ->add('description', TextType::class, array(
                'label' => '',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                )
            ))
            ->add('role', ChoiceType::class, array(
                'label' => 'admin-filter-typeofuser',
                'choices' => $roles,
                'required' => true,
                ))
            ->add('predefineddata', ChoiceType::class, array(
                'label' => 'admin-filter-predefined-date',
                'required' => false,
                'empty_value' => Text::get('admin-filter-predefined-date-choose'),
                'choices' => $antiquity,
            ))
            ->add('startdate', DatepickerType::class, array(
                'label' => 'regular-date_in',
                'required' => false,
            ))
            ->add('enddate', DatepickerType::class, array(
                'label' => 'regular-date_out',
                'required' => false,
            ))
            ->add('projects', TypeaheadType::class, [
                'type' => 'multiple',
                'label' => 'admin-projects',
                'value_field' => 'name',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'sources' => 'project'
            ])
            ->add('calls', TypeaheadType::class, [
                'type' => 'multiple',
                'label' => 'admin-calls',
                'value_field' => 'name',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'sources' => 'call'
            ])
            ->add('channels', TypeaheadType::class, [
                'type' => 'multiple',
                'label' => 'admin-channels',
                'value_field' => 'name',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'sources' => 'channel'
            ])
            ->add('matchers', TypeaheadType::class, [
                'type' => 'multiple',
                'label' => 'admin-matchers',
                'value_field' => 'name',
                'disabled' => $this->getReadonly(),
                'required' => false,
                'sources' => 'matcher'
            ])
            ->add('project_status', ChoiceType::class, array(
                'label' => 'admin-filter-project-status',
                'required' => false,
                'choices' => Project::status(),
            ))
            ->add('invest_status', ChoiceType::class, array(
                'label' => 'admin-filter-invest-status',
                'required' => false,
                'choices' => Invest::status(),
            ))
            ->add('donor_status', ChoiceType::class, array(
                'label' => 'admin-filter-donor-status',
                'required' => false,
                'choices' => $donor_status
            ))
            ->add('typeofdonor', ChoiceType::class, array(
                'label' => 'admin-filter-typeofdonor',
                'required' => false,
                'choices' => $typeofdonor,
            ))
            ->add('foundationdonor', ChoiceType::class, array(
                'required' => false,
                'label' => 'admin-filter-type-foundation-donor',
                'choices' => [Text::get('admin-no'), Text::get('admin-yes')]
            ))
            ->add('wallet', ChoiceType::class, array(
                'required' => false,
                'label' => Text::get('admin-user-wallet-amount'),
                'choices' => [Text::get('admin-no'), Text::get('admin-yes')]
            ))
            ->add('cert', ChoiceType::class, array(
                'required' => false,
                'label' => Text::get('home-advantages-certificates-title'),
                'choices' => [Text::get('admin-no'), Text::get('admin-yes')]
            ))
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

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();
        $model->rebuildData($data, array_keys($form->all()));

        $model->projects = array();
        $model->calls = array();
        $model->channels = array();
        $model->matchers = array();
        $model->footprints = array();
        $model->sdgs = array();

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

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
