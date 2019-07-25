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
            ->add('blockquote', 'text', [
                'label' => 'admin-title-blockquote',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])
            ->add('type', 'choice', array(
                'label' => 'admin-title-type',
                'required' => false,
                'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => Workshop::getListTypes()
            ))
           ->add('description', 'markdown', [
                'label' => 'regular-description',
                'required' => true,
                'attr' => [
                    'rows' => 8
                ]
            ])
           ->add('date_in', 'datepicker', array(
                'label' => 'admin-title-date-in',
                'constraints' => array(new Constraints\NotBlank()),
            ))
           ->add('date_out', 'datepicker', array(
                'label' => 'admin-title-date-out',
                'constraints' => array(new Constraints\NotBlank()),
            ))
            ->add('schedule', 'text', [
                'label' => 'regular-schedule',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('url', 'text', [
                'label' => 'regular-url',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])
           
            ->add('header_image', 'dropfiles', array(
                'required' => false,
                'limit' => 1,
                'data' => [$workshop->header_image ? $workshop->getHeaderImage() : null],
                'label' => 'admin-title-header-image',
                'accepted_files' => 'image/jpeg,image/gif,image/png,image/svg+xml',
                'url' => '/api/workshops/images',
                'constraints' => array(
                    new Constraints\Count(array('max' => 1))
                )
            ))
            ->add('venue', 'text', [
                'label' => 'admin-title-venue',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('city', 'text', [
                'label' => 'regular-city',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('how_to_get', 'text', [
                'label' => 'regular-how-to-get',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('map_iframe', 'text', [
                'label' => 'regular-map-iframe',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('schedule_file_url', 'text', [
                'label' => 'regular-schedule-file',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('call_id', 'text', [
                'label' => 'regular-call',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
             ->add('call_id', 'typeahead', [
                'label' => 'regular-call',
                'row_class' => 'extra',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'sources' => 'call',
                'text' => ($workshop && $workshop->getCall()) ? $workshop->getCall()->name : null
            ])

            /*->add('pool_image', 'dropfiles', [
                'label' => 'story-field-pool-image',
                'disabled' => $this->getReadonly(),
                'data' => $story->getPoolImage(),
                'url' => '/api/stories/images',
                'required' => false,
                'limit' => 1,
                'constraints' => [
                        new Constraints\Count(['max' => 1]),
                    ]

            ])
            ->add('review', 'text', [
                'label' => 'admin-stories-review',
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly()
            ])
            ->add('url', 'url', [
                'label' => 'regular-url',
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly()
            ])
            ->add('project', 'typeahead', [
                'label' => 'admin-project',
                'row_class' => 'extra',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'sources' => 'project',
                'text' => ($story && $story->getProject()) ? $story->getProject()->name : null
            ])
            ->add('lang', 'choice', array(
                'label' => 'regular-lang',
                'row_class' => 'extra',
                'choices' => Lang::listAll('name', false)
            ))
            ->add('pool', 'boolean', array(
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-pool', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))
            ->add('landing_pitch', 'boolean', array(
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-landing_pitch', // Form has integrated translations
                'color' => 'lilac', // bootstrap label-* (default, success, ...)
            ))
            ->add('landing_match', 'boolean', array(
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-landing_match', // Form has integrated translations
                'color' => 'lilac', // bootstrap label-* (default, success, ...)
            ))
            ->add('type', 'choice', array(
                'label' => 'admin-stories-type',
                'required' => true,
                'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => Stories::getListTypes(),
                'constraints' => [
                    new Constraints\NotBlank()
                ]
            ))
            ->add('sphere', 'choice', array(
                'label' => 'admin-title-sphere',
                'required' => true,
                // 'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => array_column(Sphere::getAll(), 'name', 'id'),
                'constraints' => [
                    new Constraints\NotBlank()
                ]
            ))
            ->add('active', 'boolean', array(
                'required' => false,
                'row_class' => 'extra',
                'disabled' => $this->getReadonly(),
                'label' => 'admin-stories-active', // Form has integrated translations
                'color' => 'cyan', // bootstrap label-* (default, success, ...)
            ))*/
            ;


        return $this;
    }

}
