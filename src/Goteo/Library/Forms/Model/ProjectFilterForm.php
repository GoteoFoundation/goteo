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
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Model\Image;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Goteo\Application\Session;
use Goteo\Library\Forms\FormModelException;
use Symfony\Component\Form\FormInterface;
use Goteo\Model\Project;

class ProjectFilterForm extends AbstractFormProcessor {

    public function createForm() {

        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $filter = $this->getModel();
        $data = $options['data'];

        $antiquity = [
            '0' => Text::get('admin-filter-last-week'),
            '1' => Text::get('admin-filter-last-month'),
            '2' => Text::get('admin-filter-last-year'),
            '3' => Text::get('admin-filter-from-new-year'),
            '4' => Text::get('admin-filter-previous-year'),
            '5' => Text::get('admin-filter-two-years-ago')
        ];

        $roles = [
            'donor' => Text::get('admin-filter-donor') ,
            'promoter' => Text::get('admin-filter-promoter') ,
            'matcher' => Text::get('admin-filter-matcher'),
            'test' => Text::get('admin-filter-test')
        ];
        
        $typeofdonor = [
            'unique' => Text::get('admin-filter-type-unique'),
            'multidonor' => Text::get('admin-filter-type-multidonor'),
            'all' => Text::get('admin-filter-type-all')
        ];

        $builder
            ->add('name', 'text', array(
                'label' => 'regular-title',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 4)),
                ),
            ))
            ->add('description', 'text', array(
                'label' => '',
                'required' => true,
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Length(array('min' => 10)),
                )
            ))
            // ->add() // interests
            ->add('predefineddata', 'choice', array(
                'label' => 'admin-filter-predefined-date',
                'required' => false,
                'empty_value' => Text::get('admin-filter-predefined-date-choose'),
                'choices' => $antiquity,
            ))
            ->add('startdate', 'datepicker', array(
                'label' => 'regular-date_in',
                'required' => false,
            )) 
            ->add('enddate', 'datepicker', array(
                'label' => 'regular-date_out',
                'required' => false,
            ))
            ->add('role', 'choice', array(
                'label' => 'admin-filter-typeofdonor',
                'choices' => $roles,
                'required' => true,
            ))
            // ->add($builder->create('admin-filters-dependent', 'form', array(
            //         'virtual' => true,
            //         'label' => false
            //     ))
                ->add('projects', 'typeahead', [
                    'label' => 'admin-projects',
                    'disabled' => $this->getReadonly(),
                    'required' => false,
                    'sources' => 'project'
                ])
                ->add('calls', 'typeahead', [
                    'label' => 'admin-calls',
                    'disabled' => $this->getReadonly(),
                    'required' => false,
                    'sources' => 'call'
                ])
                ->add('matchers', 'typeahead', [
                    'label' => 'admin-matchers',
                    'disabled' => $this->getReadonly(),
                    'required' => false,
                    'sources' => 'matcher'
                ])
                ->add('status', 'choice', array(
                    'label' => 'regular-status',
                    'required' => false,
                    'choices' => Project::status(),
                ))
                ->add('typeofdonor', 'choice', array(
                    'label' => 'admin-filter-typeofdonor',
                    'required' => false,
                    'choices' => $typeofdonor,
                ))
                ->add('foundationdonor', 'boolean', array(
                    'required' => false,
                    'label' => 'admin-filter-type-foundation-donor',
                    'color' => 'cyan',
                ))
                ->add('wallet', 'boolean', array(
                    'required' => false,
                    'label' => Text::get('admin-user-wallet-amount'), 
                    'color' => 'cyan',
                ))
                ->add('cert', 'boolean', array(
                    'required' => false,
                    'label' => Text::get('home-advantages-certificates-title'),
                    'color' => 'cyan',
                ))
                ->add('project_location', 'location', [
                    'label' => 'overview-field-project_location',
                    'disabled' => $this->getReadonly(),
                    'location_class' => 'Goteo\Model\Project\ProjectLocation',
                    'required' => false,
                    'pre_addon' => '<i class="fa fa-globe"></i>',
                ])
            // )
            ->add('submit', 'submit', [
                'label' => 'regular-submit',
                'attr' => ['class' => 'btn btn-cyan'],
                'icon_class' => 'fa fa-save'
            ])
            ;
        
        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $model = $this->getModel();
        $model->rebuildData($data, array_keys($form->all()));
        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
