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

use Goteo\Library\Forms\FormProcessorInterface;
use Goteo\Library\Forms\AbstractFormProcessor;
use Goteo\Library\Forms\FormModelException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;
use Goteo\Library\Text;

class ProjectPersonalForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function getConstraints($field) {
        $constraints = [];
        if($this->getFullValidation()) {
            if($field === 'phone') {
                $constraints[] = new Constraints\NotBlank();
            }
        }
        return $constraints;
    }

    public function createForm() {

        $this->getBuilder()
            ->add('contract_name', 'text', [
                'label' => 'personal-field-contract_name',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('contract_name'),
                'attr' => ['help' => Text::get('tooltip-project-contract_name')]
            ])
            ->add('contract_nif', 'text', [
                'label' => 'personal-field-contract_nif',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('contract_nif'),
                'attr' => ['help' => Text::get('tooltip-project-contract_nif')]
            ])
            ->add('contract_birthdate', 'datepicker', [
                'label' => 'personal-field-contract_birthdate',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('contract_birthdate'),
                'attr' => ['help' => Text::get('tooltip-project-contract_birthdate')]
            ])
            ->add('phone', 'text', [
                'label' => 'personal-field-phone',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('phone'),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-phone')]
            ])
            ->add('entity_name', 'text', [
                'label' => 'project-personal-field-entity_name',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('entity_name'),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-personal-entity_name')]
            ])
            ->add('paypal', 'text', [
                'label' => 'contract-paypal_account',
                'constraints' => $this->getConstraints('paypal'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-paypal')]
            ])
            ->add('bank', 'text', [
                'label' => 'contract-bank_account',
                'constraints' => $this->getConstraints('bank'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-bank')]
            ])
            ;
        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {
        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $user = $this->getModel();
        $user->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$user->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        $account = $this->getOption('account');
        $account->rebuildData($form->getData(), array_keys($form->all()));

        $errors = [];
        if (!$account->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }


}
