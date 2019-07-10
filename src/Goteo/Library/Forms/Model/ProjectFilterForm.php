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
            '1' => Text::get('admin-filter-last-week'),
            '2' => Text::get('admin-filter-last-month'),
            '3' => Text::get('admin-filter-last-year'),
            '4' => Text::get('admin-filter-from-new-year'),
            '5' => Text::get('admin-filter-previous-year'),
            '6' => Text::get('admin-filter-two-years-ago')
        ];

        $roles = [
            '0' => Text::get('admin-filter-donor') ,
            '1' => Text::get('admin-filter-promoter') ,
            '2' => Text::get('admin-filter-matcher'),
            '3' => Text::get('admin-filter-test')
        ];
        
        $typeofdonor = [
            '0' => Text::get('admin-filter-type-unique'),
            '1' => Text::get('admin-filter-type-multidonor'),
            '2' => Text::get('admin-filter-type-foundation-donor'),
            '3' => Text::get('admin-filter-type-all')
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
            // ->add() // interests
            ->add('startdate', 'datepicker', array(
                'label' => 'regular-date_in',
                'required' => false,
            )) 
            ->add('enddate', 'datepicker', array(
                'label' => 'regular-date_out',
                'required' => false,
            ))
            ->add('predefineddata', 'choice', array(
                'label' => 'admin-filter-predefined-date',
                'required' => false,
                'choices' => $antiquity,
            )) // fechas predefinidas
            ->add('roles', 'choice', array(
                'label' => 'admin-filter-typeofdonor',
                'choices' => $roles,
                'required' => true,
            ))
            ->add($builder->create('admin-filters-dependent', 'form', array(
                    'virtual' => true,
                    'label' => false
                ))
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
            )
            ;
        
        return $this;
    }

}
