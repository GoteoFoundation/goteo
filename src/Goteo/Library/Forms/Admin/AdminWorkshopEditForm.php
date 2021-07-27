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
use Goteo\Model\Stories;
use Goteo\Model\Sphere;
use Goteo\Model\Workshop;
use Goteo\Model\Workshop\WorkshopLocation;
use Goteo\Library\Forms\FormModelException;
use Goteo\Application\Lang;

class AdminWorkshopEditForm extends AbstractFormProcessor {

    public function createForm() {
        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $workshop = $this->getModel();
        $data = $options['data'];
        parent::createForm();
        $builder
            ->add('title', 'text', [
                'label' => 'regular-title',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('subtitle', 'text', [
                'label' => 'admin-title-subtitle',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('online', 'boolean', array(
                'required' => false,
                'disabled' => $this->getReadonly(),
                'label' => 'admin-online', //
                'color' => 'cyan'
            ))
            ->add('blockquote', 'textarea', [
                'label' => 'admin-title-blockquote',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'attr' => [
                    'rows' => 2
                ]
            ])
           ->add('description', 'markdown', [
                'label' => 'regular-description',
                'required' => true,
                'attr' => [
                    'rows' => 8
                ]
            ])
           ->add('workshop_location', 'location', [
                'label' => 'overview-field-project_location',
                'type' => 'workshop', // API geoloc
                'item' => $this->getModel()->id,
                'disabled' => $this->getReadonly(),
                'location_object' => WorkshopLocation::get($workshop),
                'location_class' => 'Goteo\Model\Workshop\WorkshopLocation',
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>',
                'attr' => ['help' => Text::get('tooltip-project-project_location')]
            ])
           ->add('date_in', 'datepicker', array(
                'label' => 'admin-title-date-in',
                'required' => true,
                'constraints' => array(new Constraints\NotBlank()),
            ))
           ->add('date_out', 'datepicker', array(
                'label' => 'admin-title-date-out',
                'required' => false,
                'constraints' => array(new Constraints\NotBlank()),
            ))
            ->add('schedule', 'text', [
                'label' => 'regular-schedule',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])
            ->add('url', 'text', [
                'label' => 'admin-title-url-inscription',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])
           
            ->add('header_image', 'dropfiles', array(
                'required' => false,
                'limit' => 1,
                'data' => [$workshop->header_image ? $workshop->getHeaderImage() : null],
                'label' => 'admin-title-header-image',
                'accepted_files' => 'image/jpeg,image/gif,image/png,image/svg+xml',
                'constraints' => array(
                    new Constraints\Count(array('max' => 1))
                )
            ))
            ->add('venue', 'text', [
                'label' => 'admin-title-venue',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])
            ->add('venue_address', 'textarea', [
                'label' => 'admin-title-venue-address',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'attr' => [
                    'rows' => 4
                ]
            ])
            ->add('city', 'text', [
                'label' => 'admin-title-city',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])    
            ->add('how_to_get', 'markdown', [
                'label' => 'admin-title-how-to-get',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'attr' => [
                    'rows' => 4
                ]
            ])
            ->add('map_iframe', 'textarea', [
                'label' => 'admin-title-iframe',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'attr' => [
                    'rows' => 4
                ]
            ])
            ->add('schedule_file_url', 'text', [
                'label' => 'admin-title-schedule-file',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])
            ->add('event_type', 'choice', array(
                'label' => 'admin-title-type',
                'required' => false,
                'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => Workshop::getListEventTypes()
            ))
             ->add('call_id', 'typeahead', [
                'label' => 'admin-title-call',
                'row_class' => 'extra',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'sources' => 'call',
                'text' => ($workshop && $workshop->getCall()) ? $workshop->getCall()->name : null
            ])

            
            ;


        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();

        // Instance of workshop location
        $workshop_location=$data['workshop_location'];
        $data['workshop_location'] = $data['workshop_location']->location ? $data['workshop_location']->location : $data['workshop_location']->name;
       
        $model = $this->getModel();
        $model->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if($workshop_location instanceOf WorkshopLocation) {
            $workshop_location->id = $model->id;
            if($workshop_location->save($errors)) {
                //
            } else {
                $fail = true;
            }

        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
