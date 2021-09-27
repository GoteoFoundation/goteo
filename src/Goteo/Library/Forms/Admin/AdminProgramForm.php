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
use Goteo\Library\Forms\FormModelException;

use Goteo\Model\Node\NodeProgram;

class AdminProgramForm extends AbstractFormProcessor {

    public function getConstraints($field) {
        return [new Constraints\NotBlank()];
    }

    public function createForm() {
        $model = $this->getModel();

        $builder = $this->getBuilder();
        $options = $builder->getOptions();
        $defaults = $options['data'];

        $builder
            ->add('title', 'text', [
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('title'),
                'required' => true,
                'label' => 'regular-title'
            ])
            ->add('description', 'text', [
                'disabled' => $this->getReadonly(),
                'required' => false,
                'label' => 'regular-description'
            ])
            ->add('modal_description', 'textarea', [
                'disabled' => $this->getReadonly(),
                'required' => false,
                'label' => 'regular-modal-decription'
            ])
            ->add('header', 'dropfiles', array(
                'required' => false,
                'limit' => 1,
                'data' => [$model->header ? $model->getHeader() : null],
                'label' => 'admin-title-header-image',
                'accepted_files' => 'image/jpeg,image/gif,image/png,image/svg+xml'
            ))
            ->add('icon', 'text', [
              'disabled' => $this->getReadonly(),
              'required' => false,
              'label' => 'regular-icon'
            ])
            ->add('action', 'text', [
              'disabled' => $this->getReadonly(),
              'required' => false,
              'label' => 'regular-action'
            ])
            ->add('action_url', 'text', [
              'disabled' => $this->getReadonly(),
              'required' => false,
              'label' => 'regular-action_url'
            ])
            ->add('date', 'datepicker', [
              'disabled' => $this->getReadonly(),
              'required' => true,
              'label' => 'regular-date'
            ])
            ;

        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {

        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) {
            throw new FormModelException(Text::get('form-has-errors'));
        }

        $data = $form->getData();
        $model = $this->getModel();
        
        if($data['header'] && is_array($data['header'])) {
            if ($data['header']['removed']) {
                if ($model->header == $data['header']['removed'][0]->id)
                    $model->header = null;
            } 
            
            if ($data['header']['uploads']) {
                $model->header = $data['header']['uploads'][0];
            }
        }

        unset($data['header']);
        
        $model->rebuildData($data, array_keys($form->all()));
        $errors = [];
        if (!$model->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }
        return $this;
    }
}
