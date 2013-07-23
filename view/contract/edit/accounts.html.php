<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$contract = $this['contract'];
$errors = $contract->errors[$this['step']] ?: array();
$okeys  = $contract->okeys[$this['step']] ?: array();

$superform = array(
    'level'         => $this['level'],
    'action'        => '',
    'method'        => 'post',
    'title'         => Text::get('contract-accounts-main-header'),
    'hint'          => Text::get('guide-contract-accounts'),
    'class'         => 'aqua',        
    'elements'      => array(
        'process_accounts' => array (
            'type' => 'hidden',
            'value' => 'accounts'
        ),
        
        'bank' => array(
            'type'      => 'textbox',
            'title'     => Text::get('contract-bank_account'),
            'required'  => true,
            'hint'      => Text::get('tooltip-contract-bank_account'),
            'value'     => $contract->bank,
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array()
        ),
        
        'bank_owner' => array(
            'type'      => 'textbox',
            'title'     => Text::get('contract-bank_owner'),
            'required'  => true,
            'hint'      => Text::get('tooltip-contract-bank_owner'),
            'value'     => $contract->bank_owner,
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array()
        ),
        
        'paypal' => array(
            'type'      => 'textbox',
            'title'     => Text::get('contract-paypal_account'),
            'required'  => true,
            'hint'      => Text::get('tooltip-contract-paypal_account'),
            'value'     => $contract->paypal,
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array()
        ),
        
        'paypal_owner' => array(
            'type'      => 'textbox',
            'title'     => Text::get('contract-paypal_owner'),
            'required'  => true,
            'hint'      => Text::get('tooltip-contract-paypal_owner'),
            'value'     => $contract->paypal_owner,
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array()
        ),
        
        
        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'errors' => array(
                    'title' => Text::get('form-footer-errors_title'),
                    'view'  => new View('view/contract/edit/errors.html.php', array(
                        'contract'   => $contract,
                        'step'      => $this['step']
                    ))                    
                ),
                'buttons'  => array(
                    'type'  => 'group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'submit',
                            'name'  => 'view-step-additional',
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
    
    if (!empty($this['errors'][$this['step']][$id])) {
        $element['errors'] = arrray();
    }
    
}

echo new SuperForm($superform);