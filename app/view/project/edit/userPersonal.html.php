<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Core\View;

$project = $vars['project'];
$errors = $project->errors[$vars['step']] ?: array();
$okeys  = $project->okeys[$vars['step']] ?: array();
$account = $vars['account'];

// esto lo hago para que proyectos en convocatoria no les salga para poner cuenta paypal
$campos_cuentas  = array();

if (!isset($project->called)) {
    $campos_cuentas['paypal'] = array(
        'type'      => 'textbox',
        'title'     => Text::get('contract-paypal_account'),
        'hint'      => Text::get('tooltip-project-paypal'),
        'errors'    => !empty($errors['paypal']) ? array($errors['paypal']) : array(),
        'ok'        => !empty($okeys['paypal']) ? array($okeys['paypal']) : array(),
        'value'     => $account->paypal
    );

    $campos_cuentas['paypal_advice'] = array(
        'type'      => 'html',
        'class'     => 'inline',
        'html'      => Text::get('tooltip-project-paypal')
    );
}

$campos_cuentas['bank'] = array(
    'type'      => 'textbox',
    'title'     => Text::get('contract-bank_account'),
    'hint'      => Text::get('tooltip-project-bank'),
    'errors'    => !empty($errors['bank']) ? array($errors['bank']) : array(),
    'ok'        => !empty($okeys['bank']) ? array($okeys['bank']) : array(),
    'value'     => $account->bank
);



echo SuperForm::get(array(

    'level'         => $vars['level'],
    'method'        => 'post',
    'title'         => Text::get('personal-main-header'),
    'hint'          => Text::get('guide-project-contract-information'),
    'elements'      => array(
        'process_userPersonal' => array (
            'type' => 'hidden',
            'value' => 'userPersonal'
        ),

        'anchor-personal' => array(
            'type' => 'html',
            'html' => '<a name="personal"></a>'
        ),

        'contract' => array(
            'type'      => 'group',
            'title'     => Text::get('personal-field-contract_data'),
            'hint'      => Text::get('tooltip-project-contract_data'),
            'children'  => array(
                'contract_name' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'size'      => 20,
                    'title'     => Text::get('personal-field-contract_name'),
                    'hint'      => Text::get('tooltip-project-contract_name'),
                    'errors'    => !empty($errors['contract_name']) ? array($errors['contract_name']) : array(),
                    'ok'        => !empty($okeys['contract_name']) ? array($okeys['contract_name']) : array(),
                    'value'     => $project->contract_name
                ),

                'contract_nif' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('personal-field-contract_nif'),
                    'size'      => 9,
                    'hint'      => Text::get('tooltip-project-contract_nif'),
                    'errors'    => !empty($errors['contract_nif']) ? array($errors['contract_nif']) : array(),
                    'ok'        => !empty($okeys['contract_nif']) ? array($okeys['contract_nif']) : array(),
                    'value'     => $project->contract_nif
                ),

                'contract_birthdate'  => array(
                    'type'      => 'datebox',
                    'required'  => true,
                    'size'      => 8,
                    'title'     => Text::get('personal-field-contract_birthdate'),
                    'hint'      => Text::get('tooltip-project-contract_birthdate'),
                    'errors'    => !empty($errors['contract_birthdate']) ? array($errors['contract_birthdate']) : array(),
                    'ok'        => !empty($okeys['contract_birthdate']) ? array($okeys['contract_birthdate']) : array(),
                    'value'     => $project->contract_birthdate
                ),

                'phone' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('personal-field-phone'),
                    'size'      => 15,
                    'hint'      => Text::get('tooltip-project-phone'),
                    'errors'    => !empty($errors['phone']) ? array($errors['phone']) : array(),
                    'ok'        => !empty($okeys['phone']) ? array($okeys['phone']) : array(),
                    'value'     => $project->phone
                ),

                'contract_birthdate'  => array(
                    'type'      => 'datebox',
                    'required'  => true,
                    'size'      => 8,
                    'title'     => Text::get('personal-field-contract_birthdate'),
                    'hint'      => Text::get('tooltip-project-contract_birthdate'),
                    'errors'    => !empty($errors['contract_birthdate']) ? array($errors['contract_birthdate']) : array(),
                    'ok'        => !empty($okeys['contract_birthdate']) ? array($okeys['contract_birthdate']) : array(),
                    'value'     => $project->contract_birthdate
                ),

                'entity_name' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => false,
                    'title'     => Text::get('project-personal-field-entity_name'),
                    'hint'      => Text::get('tooltip-project-personal-entity_name'),
                    'errors'    => !empty($errors['entity_name']) ? array($errors['entity_name']) : array(),
                    'ok'        => !empty($okeys['entity_name']) ? array($okeys['entity_name']) : array(),
                    'value'     => $project->entity_name
                ),

            )
        ),

        /* Cuentas */
        'accounts' => array(
            'type'      => 'group',
            'title'     => Text::get('personal-field-accounts'),
            'children'  => $campos_cuentas
        ),

        /* Radio de domicilio postal igual o diferente
         * Aligerando superform
        'post_address-radioset' => array(
            'type'      => 'group',
            'class'     => 'inline',
            'title'     => Text::get('personal-field-post_address'),
            'hint'      => Text::get('tooltip-project-post_address'),
            'children'  => array(
                'post_address-radio-same' =>  array(
                    'name'  => 'secondary_address',
                    'value' => false,
                    'type'  => 'radio',
                    'class' => 'inline',
                    'label' => Text::get('personal-field-post_address-same'),
                    'id'    => 'post_address-radio-same',
                    'checked' => !$project->secondary_address ? true : false,
                    'children' => array(
                        // Children vacio si es igual
                        'post_address-same' => array(
                            'type' => 'hidden',
                            'name' => "post_address-same",
                            'value' => 'same'
                        ),
                    )
                ),
                'post_address-radio-different' =>  array(
                    'name'  => 'secondary_address',
                    'value' => true,
                    'type'  => 'radio',
                    'class' => 'inline',
                    'label' => Text::get('personal-field-post_address-different'),
                    'id'    => 'post_address-radio-different',
                    'checked' => $project->secondary_address ? true : false,
                    'children' => array(
                        // Domicilio postal (a desplegar si es diferente)
                        'post_address' => array(
                            'type'      => 'textbox',
                            'class'     => 'inline',
                            'title'     => Text::get('personal-field-address'),
                            'rows'      => 6,
                            'cols'      => 40,
                            'hint'      => Text::get('tooltip-project-post_address'),
                            'errors'    => !empty($errors['post_address']) ? array($errors['post_address']) : array(),
                            'ok'        => !empty($okeys['post_address']) ? array($okeys['post_address']) : array(),
                            'value'     => $project->post_address
                        ),

                        'post_zipcode' => array(
                            'type'      => 'textbox',
                            'class'     => 'inline',
                            'title'     => Text::get('personal-field-zipcode'),
                            'size'      => 7,
                            'hint'      => Text::get('tooltip-project-post_address'),
                            'errors'    => !empty($errors['post_zipcode']) ? array($errors['post_zipcode']) : array(),
                            'ok'        => !empty($okeys['post_zipcode']) ? array($okeys['post_zipcode']) : array(),
                            'value'     => $project->post_zipcode
                        ),

                        'post_location' => array(
                            'type'      => 'textbox',
                            'class'     => 'inline',
                            'title'     => Text::get('personal-field-location'),
                            'size'      => 25,
                            'hint'      => Text::get('tooltip-project-post_address'),
                            'errors'    => !empty($errors['post_location']) ? array($errors['post_location']) : array(),
                            'ok'        => !empty($okeys['post_location']) ? array($okeys['post_location']) : array(),
                            'value'     => $project->post_location
                        ),

                        'post_country' => array(
                            'type'      => 'textbox',
                            'class'     => 'inline',
                            'title'     => Text::get('personal-field-country'),
                            'size'      => 25,
                            'hint'      => Text::get('tooltip-project-post_address'),
                            'errors'    => !empty($errors['post_country']) ? array($errors['post_country']) : array(),
                            'ok'        => !empty($okeys['post_country']) ? array($okeys['post_country']) : array(),
                            'value'     => $project->post_country
                        )
                    )
                ),
            )
        ),
        */

        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'errors' => array(
                    'title' => Text::get('form-footer-errors_title'),
                    'view'  => new View('project/edit/errors.html.php', array(
                        'project'   => $project,
                        'step'      => $vars['step']
                    ))
                ),
                'buttons'  => array(
                    'type'  => 'group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'submit',
                            'name'  => 'view-step-'.$vars['next'],
                            'label' => Text::get('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )

        )

    )

));
