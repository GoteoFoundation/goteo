<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$contract = $vars['contract'];
$errors = $contract->errors[$vars['step']] ?: array();
$okeys  = $contract->okeys[$vars['step']] ?: array();

$superform = array(
    'level'         => $vars['level'],
    'action'        => '',
    'method'        => 'post',
    'title'         => Text::get('contract-step-accounts'),
    'hint'          => Text::get('guide-contract-accounts'),
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
            'title'     => Text::get('contract-paypal_owner'),
            'class'     => 'inline',
            'required'  => (!empty($contract->paypal)),
            'hint'      => Text::get('tooltip-contract-paypal_owner'),
            'value'     => $contract->paypal_owner,
            'errors'    => !empty($errors['paypal_owner']) ? array($errors['paypal_owner']) : array(),
            'ok'        => !empty($okeys['paypal_owner']) ? array($okeys['paypal_owner']) : array()
        ),

        'bank_owner' => array(
            'type'      => 'TextBox',
            'title'     => Text::get('contract-bank_owner'),
            'required'  => true,
            'hint'      => Text::get('tooltip-contract-bank_owner'),
            'value'     => $contract->bank_owner,
            'errors'    => !empty($errors['bank_owner']) ? array($errors['bank_owner']) : array(),
            'ok'        => !empty($okeys['bank_owner']) ? array($okeys['bank_owner']) : array()
        ),

        'bank' => array(
            'type'      => 'TextBox',
            'title'     => Text::get('contract-bank_account'),
            'class'     => 'inline',
            'required'  => true,
            'hint'      => Text::get('tooltip-contract-bank_account'),
            'value'     => $contract->bank,
            'errors'    => !empty($errors['bank']) ? array($errors['bank']) : array(),
            'ok'        => !empty($okeys['bank']) ? array($okeys['bank']) : array()
        ),

        'footer' => array(
            'type'      => 'Group',
            'children'  => array(
                'errors' => array(
                    'title' => Text::get('form-footer-errors_title'),
                    'view'  => new View('contract/edit/errors.html.php', array(
                        'contract'   => $contract,
                        'step'      => $vars['step']
                    ))
                ),
                'buttons'  => array(
                    'type'  => 'Group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'submit',
                            'name'  => 'view-step-documents',
                            'label' => Text::get('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )

        )

    )

);


foreach ($superform['elements'] as $id => &$element) {

    if (!empty($vars['errors'][$vars['step']][$id])) {
        $element['errors'] = arrray();
    }

}

echo SuperForm::get($superform);
