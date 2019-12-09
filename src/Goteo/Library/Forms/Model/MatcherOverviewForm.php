<?php

/** 
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library\Forms\Model;

use Symfony\Component\Form\FormInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;
use Goteo\Library\Forms\FormModelException;
use Goteo\Model\Matcher\MatcherLocation;

class MatcherOverviewForm extends AbstractFormProcessor
{

    public function getConstraints($field)
    {
        $constraints = [];
        if ($field === 'name' || $field === 'description') {
            $constraints[] = new Constraints\NotBlank();
        }
        return $constraints;
    }

    public function getDefaults($sanitize = true)
    {
        $data = parent::getDefaults($sanitize);
        return $data;
    }


    public function createForm()
    {
        $matcher = $this->getModel();

        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $defaults = $options['data'];
        $builder
            ->add(
                'name', 'text', [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('name'),
                'label' => 'regular-name'
                ]
            )
            ->add(
                'description', 'textarea', [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('name'),
                'label' => 'regular-description',
                'attr' => [
                    'placeholder' => Text::get('regular-write-description')
                    ]
                ]
            )
            ->add(
                'location', 'location', [
                'label' => 'personal-field-location',
                'constraints' => $this->getConstraints('location'),
                'disabled' => $this->getReadonly(),
                'type' => 'matcher',
                'item' => $matcher->id,
                'location_object' => MatcherLocation::get($matcher),
                'location_class' => 'Goteo\Model\Matcher\MatcherLocation',
                'required' => false,
                'pre_addon' => '<i class="fa fa-globe"></i>'
                ]
            )
            ->add(
                'logo', 'dropfiles', array(
                'required' => true,
                'limit' => 1,
                'label' => 'admin-title-avatar',
                'accepted_files' => 'image/jpeg,image/png,image/svg+xml',
                'url' => '/api/matchers/images',
                'constraints' => array(
                  new Constraints\Count(array('max' => 1))
                )
                )
            )
            ->add(
                'save', 'submit', [
                'label' => 'regular-save',
                'attr' => [
                    'class' => 'btn btn-lg btn-lilac'
                ]
                ]
            );

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false)
    {
        if (!$form) { 
            $form = $this->getBuilder()->getForm();
        }
        if (!$form->isValid() && !$force_save) { 
            throw new FormModelException(Text::get('form-has-errors'));
        }

        $errors = [];
        $data = $form->getData();
        $matcher = $this->getModel();
        $matcher->location = $data['location'] ? $data['location'] : '';
        
        if (is_array($data['logo'])) {
            $data['logo'] = reset($data['logo']);
        }
        $matcher->logo = $data['logo'];
        if ($matcher->logo && $err = $matcher->logo->getUploadError()) {
            throw new FormModelException(Text::get('form-sent-error', $err));
        }
        
        $matcher->rebuildData($data, array_keys($form->all()));

        if (!$matcher->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(',', array_map('implode', $errors))));
        }

        if (!$form->isValid()) {
            throw new FormModelException(Text::get('form-has-errors'));
        }

        return $this;
    }
}
