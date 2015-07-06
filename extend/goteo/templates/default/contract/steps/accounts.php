<?php


$this->layout('contract/edit');


$contract = $this->contract;
$step = $this->step;
$errors = $this->errors;
$errors = $contract->errors[$step] ?: array();
$okeys  = $contract->okeys[$step] ?: array();

$superform = array(
    'level'         => 3,
    'action'        => '',
    'method'        => 'post',
    'title'         => $this->text('contract-step-accounts'),
    'hint'          => $this->text('guide-contract-accounts'),
    'class'         => 'aqua',
    'elements'      => array(
        'process_accounts' => array (
            'type' => 'Hidden',
            'value' => 'accounts'
        ),

        'paypal' => array (
            'type' => 'HTML',
            'title' => 'Cuenta PayPal del proyecto:',
            'html' => $contract->paypal
        ),

        'paypal_owner' => array(
            'type'      => 'TextBox',
            'title'     => $this->text('contract-paypal_owner'),
            'class'     => 'inline',
            'required'  => (!empty($contract->paypal)),
            'hint'      => $this->text('tooltip-contract-paypal_owner'),
            'value'     => $contract->paypal_owner,
            'errors'    => !empty($errors['paypal_owner']) ? array($errors['paypal_owner']) : array(),
            'ok'        => !empty($okeys['paypal_owner']) ? array($okeys['paypal_owner']) : array()
        ),

        'bank_owner' => array(
            'type'      => 'TextBox',
            'title'     => $this->text('contract-bank_owner'),
            'required'  => true,
            'hint'      => $this->text('tooltip-contract-bank_owner'),
            'value'     => $contract->bank_owner,
            'errors'    => !empty($errors['bank_owner']) ? array($errors['bank_owner']) : array(),
            'ok'        => !empty($okeys['bank_owner']) ? array($okeys['bank_owner']) : array()
        ),

        'bank' => array(
            'type'      => 'TextBox',
            'title'     => $this->text('contract-bank_account'),
            'class'     => 'inline',
            'required'  => true,
            'hint'      => $this->text('tooltip-contract-bank_account'),
            'value'     => $contract->bank,
            'errors'    => !empty($errors['bank']) ? array($errors['bank']) : array(),
            'ok'        => !empty($okeys['bank']) ? array($okeys['bank']) : array()
        ),

        'footer' => array(
            'type'      => 'Group',
            'children'  => array(
                'errors' => array(
                    'title' => $this->text('form-footer-errors_title'),
                    'content'  => $this->insert('contract/partials/errors', array(
                        'contract'   => $contract,
                        'step'      => $step
                    ))
                ),
                'buttons'  => array(
                    'type'  => 'Group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'Button',
                            'buttontype'  => 'submit',
                            'name'  => 'step',
                            'value'  => 'documents',
                            'label' => $this->text('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )

        )

    )

);


foreach ($superform['elements'] as $id => &$element) {

    if (!empty($errors[$step][$id])) {
        $element['errors'] = array();
    }

}

$this->section('contract-edit-step');

echo \Goteo\Library\SuperForm::get($superform);

$this->replace();
