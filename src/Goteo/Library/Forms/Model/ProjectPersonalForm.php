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

    public function createForm() {

        $this->getBuilder()
            ->add('contract_name', 'text', [
                'label' => 'personal-field-contract_name',
                'constraints' => array(new Constraints\NotBlank()),
                'attr' => ['help' => Text::get('tooltip-project-contract_name')]
            ])
            ->add('contract_nif', 'text', [
                'label' => 'personal-field-contract_nif',
                'constraints' => array(new Constraints\NotBlank()),
                'attr' => ['help' => Text::get('tooltip-project-contract_nif')]
            ])
            ->add('contract_birthdate', 'datepicker', [
                'label' => 'personal-field-contract_birthdate',
                'constraints' => array(new Constraints\NotBlank()),
                'attr' => ['help' => Text::get('tooltip-project-contract_birthdate')]
            ])
            ->add('phone', 'text', [
                'label' => 'personal-field-phone',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-phone')]
            ])
            ->add('entity_name', 'text', [
                'label' => 'project-personal-field-entity_name',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-personal-entity_name')]
            ])
            ->add('paypal', 'text', [
                'label' => 'contract-paypal_account',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-paypal')]
            ])
            ->add('bank', 'text', [
                'label' => 'contract-bank_account',
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-bank')]
            ])
            ;
        return $this;
    }

    public function save(FormInterface $form = null) {
        parent::save($form);

        $account = $this->getOption('account');
        $account->rebuildData($form->getData(), array_keys($form->all()));

        $errors = [];
        if (!$account->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }
        return true;
    }


}
