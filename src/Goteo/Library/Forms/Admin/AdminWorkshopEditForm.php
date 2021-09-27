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

use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Text;
use Goteo\Model\Workshop;
use Goteo\Model\Workshop\WorkshopLocation;
use Goteo\Util\Form\Type\BooleanType;
use Goteo\Util\Form\Type\ChoiceType;
use Goteo\Util\Form\Type\DatepickerType;
use Goteo\Util\Form\Type\DropfilesType;
use Goteo\Util\Form\Type\LocationType;
use Goteo\Util\Form\Type\MarkdownType;
use Goteo\Util\Form\Type\TextareaType;
use Goteo\Util\Form\Type\TextType;
use Goteo\Util\Form\Type\TypeaheadType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;

class AdminWorkshopEditForm extends AbstractFormProcessor {

    public function createForm() {
        $builder = $this->getBuilder();
        $workshop = $this->getModel();
        parent::createForm();

        $builder
            ->add('title', TextType::class, [
                'label' => 'regular-title',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('subtitle', TextType::class, [
                'label' => 'admin-title-subtitle',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('online', BooleanType::class, array(
                'required' => false,
                'disabled' => $this->getReadonly(),
                'label' => 'admin-online',
                'color' => 'cyan'
            ))
            ->add('blockquote', TextareaType::class, [
                'label' => 'admin-title-blockquote',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'attr' => [
                    'rows' => 2
                ]
            ])
            ->add('description', MarkdownType::class, [
                'label' => 'regular-description',
                'required' => true,
                'attr' => [
                    'rows' => 8
                ]
            ])
            ->add('workshop_location', LocationType::class, [
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
            ->add('date_in', DatepickerType::class, [
                'label' => 'admin-title-date-in',
                'required' => true,
                'constraints' => [new Constraints\NotBlank()],
            ])
            ->add('date_out', DatepickerType::class, [
                'label' => 'admin-title-date-out',
                'required' => false,
                'constraints' => [new Constraints\NotBlank()],
               ])
            ->add('schedule', TextType::class, [
                'label' => 'regular-schedule',
                'required' => true,
                'disabled' => $this->getReadonly()
            ])
            ->add('url', TextType::class, [
                'label' => 'admin-title-url-inscription',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])
            ->add('header_image', DropfilesType::class, [
                'required' => false,
                'limit' => 1,
                'data' => [$workshop->header_image ? $workshop->getHeaderImage() : null],
                'label' => 'admin-title-header-image',
                'accepted_files' => 'image/jpeg,image/gif,image/png,image/svg+xml'
            ])
            ->add('venue', TextType::class, [
                'label' => 'admin-title-venue',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])
            ->add('venue_address', TextareaType::class, [
                'label' => 'admin-title-venue-address',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'attr' => [
                    'rows' => 4
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'admin-title-city',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])
            ->add('how_to_get', MarkdownType::class, [
                'label' => 'admin-title-how-to-get',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'attr' => [
                    'rows' => 4
                ]
            ])
            ->add('map_iframe', TextareaType::class, [
                'label' => 'admin-title-iframe',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'attr' => [
                    'rows' => 4
                ]
            ])
            ->add('schedule_file_url', TextType::class, [
                'label' => 'admin-title-schedule-file',
                'required' => false,
                'disabled' => $this->getReadonly()
            ])
            ->add('event_type', ChoiceType::class, [
                'label' => 'admin-title-type',
                'required' => false,
                'expanded' => true,
                'row_class' => 'extra',
                'wrap_class' => 'col-xs-6',
                'choices' => $this->getWorkshopsChoices()
            ])
            ->add('call_id', TypeaheadType::class, [
                'label' => 'admin-title-call',
                'row_class' => 'extra',
                'required' => false,
                'disabled' => $this->getReadonly(),
                'sources' => 'call',
                'text' => ($workshop && $workshop->getCall()) ? $workshop->getCall()->name : null
            ]);

        return $this;
    }

    private function getWorkshopsChoices(): array
    {
        $choices = [];

        foreach (Workshop::getListEventTypes() as $k => $v) {
            $choices[$v] = $k;
        }

        return $choices;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $workshop_location = $data['workshop_location'];
        $data['workshop_location'] = $data['workshop_location']->location ?: $data['workshop_location']->name;

        $model = $this->getModel();

        if ($data['header_image'] && is_array($data['header_image'])) {
            if (current($data['header_image']['removed'])->id == $model->header_image)
                $model->header_image = null;

            if ($data['header_image']['uploads'])
                $model->header_image = $data['header_image']['uploads'][0];
        }

        unset($data['header_image']);
        $model->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if($workshop_location instanceOf WorkshopLocation) {
            $workshop_location->id = $model->id;
            $workshop_location->save($errors);
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }

}
