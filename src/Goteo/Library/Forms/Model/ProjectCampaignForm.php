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
use Goteo\Model\User;

class ProjectCampaignForm extends AbstractFormProcessor implements FormProcessorInterface {

    public function getConstraints($field) {
        $constraints = [];
        if($field === 'paypal') {
            $constraints[] = new Constraints\Email([
                'checkMX' => true,
                'checkHost' => true
            ]);
        }
        if($this->getFullValidation()) {
            if($field === 'phone') {
                $constraints[] = new Constraints\NotBlank();
            }
        }
        return $constraints;
    }

    public function createForm() {
        $project = $this->getModel();

        $this->getBuilder()
            ->add('one_round', 'choice', [
                'disabled' => $this->getReadonly(),
                'label' => 'costs-field-select-rounds',
                'constraints' => $this->getConstraints('one_round'),
                'required' => true,
                'expanded' => true,
                'wrap_class' => 'col-xs-6',
                'choices' => [
                    '1' => Text::get('project-one-round'),
                    '0' => Text::get('project-two-rounds')
                ],
                'attr' => ['help' => '<span class="' . ($project->one_round ? '': 'hidden') . '">' . Text::get('tooltip-project-rounds') . '</span><span class="' . ($project->one_round ? 'hidden': '') . '">' . Text::get('tooltip-project-2rounds') . '</span>']
            ])
            ->add('phone', 'text', [
                'label' => 'personal-field-phone',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('phone'),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-phone')]
            ])
            ->add('paypal', 'email', [
                'label' => 'contract-paypal_account',
                'constraints' => $this->getConstraints('paypal'),
                'disabled' => $this->getReadonly(),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-paypal')]
            ])
            ->add('spread', 'textarea', [
                'label' => 'overview-field-spread',
                'disabled' => $this->getReadonly(),
                'constraints' => $this->getConstraints('spread'),
                'required' => false,
                'attr' => ['help' => Text::get('tooltip-project-spread'), 'info' => '<i class="fa fa-eye-slash"></i> '. Text::get('project-non-public-field'), 'rows' => 8]
            ])
            ;
        return $this;
    }

    public function save(FormInterface $form = null, $force_save = false) {

        if(!$form) $form = $this->getBuilder()->getForm();
        if(!$form->isValid() && !$force_save) throw new FormModelException(Text::get('form-has-errors'));

        $data = $form->getData();
        $project = $this->getModel();
        $project->rebuildData($data, array_keys($form->all()));

        $errors = [];
        if (!$project->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        $data = $form->getData();
        $account = $this->getOption('account');
        $account->rebuildData(['paypal' => $data['paypal']]);

        $errors = [];
        if (!$account->save($errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }
        $user = $this->getOption('user');
        if(!User::setPersonal($user, ['phone' => $data['phone']], true, $errors)) {
            throw new FormModelException(Text::get('form-sent-error', implode(', ',$errors)));
        }

        if(!$form->isValid()) throw new FormModelException(Text::get('form-has-errors'));

        return $this;
    }


}
