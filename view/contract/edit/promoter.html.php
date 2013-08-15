<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Core\View;

$contract = $this['contract'];
$errors = $contract->errors[$this['step']] ?: array();         
$okeys  = $contract->okeys[$this['step']] ?: array();

echo new SuperForm(array(

    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('contract-step-promoter'),
    'hint'          => Text::get('guide-contract-promoter'),    
    'elements'      => array(
        'process_promoter' => array (
            'type' => 'hidden',
            'value' => 'promoter'
        ),
        
        /* Radio Tipo de persona */
        'contract_entity-radioset' => array(
            'type'      => 'group',
            'title'     => Text::get('personal-field-contract_entity'),
            'hint'     => Text::get('tooltip-project-contract_entity'),
            'children'  => array(
                'type-person' =>  array(
                    'name'  => 'type',
                    'value' => 0,
                    'type'  => 'radio',
                    'class' => 'inline',
                    'label' => 'En su propio nombre y derecho ',
                    'checked' => $contract->type == 0,
                ),
                'type-association' =>  array(
                    'name'  => 'type',
                    'value' => 1,
                    'type'  => 'radio',
                    'class' => 'inline',
                    'label' => 'Como representante de una asociación ',
                    'checked' => $contract->type == 1,
                ),
                'type-bussines' =>  array(
                    'name'  => 'type',
                    'value' => 2,
                    'type'  => 'radio',
                    'class' => 'inline',
                    'label' => 'Como apoderado de una entidad mercantil ',
                    'checked' => $contract->type == 2,
                )
            )
        ),

        'name' => array(
            'type'      => 'textbox',
            'class'     => 'inline',
            'required'  => true,
            'title'     => Text::get('personal-field-contract_name'),
            'errors'    => !empty($errors['name']) ? array($errors['name']) : array(),
            'ok'        => !empty($okeys['name']) ? array($okeys['name']) : array(),
            'value'     => $contract->name
        ),

        'nif' => array(
            'type'      => 'textbox',
            'class'     => 'inline',
            'required'  => true,
            'title'     => Text::get('personal-field-contract_nif'),
            'errors'    => !empty($errors['nif']) ? array($errors['nif']) : array(),
            'ok'        => !empty($okeys['nif']) ? array($okeys['nif']) : array(),
            'value'     => $contract->nif
        ),

        /* Domicilio fiscal */
        'fiscaladdr' => array(
            'type'      => 'group',
            'title'     => 'Dirección fiscal',
            'children'  => array(

                'address' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('personal-field-address'),
                    'errors'    => !empty($errors['address']) ? array($errors['address']) : array(),
                    'ok'        => !empty($okeys['address']) ? array($okeys['address']) : array(),
                    'value'     => $contract->address
                ),

                'location' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('contract-field-location'),
                    'errors'    => !empty($errors['location']) ? array($errors['location']) : array(),
                    'ok'        => !empty($okeys['location']) ? array($okeys['location']) : array(),
                    'value'     => $contract->location
                ),

                'region' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('personal-field-region'),
                    'errors'    => !empty($errors['region']) ? array($errors['region']) : array(),
                    'ok'        => !empty($okeys['region']) ? array($okeys['region']) : array(),
                    'value'     => $contract->region
                ),

                'zipcode' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('personal-field-zipcode'),
                    'size'      => 7,
                    'errors'    => !empty($errors['zipcode']) ? array($errors['zipcode']) : array(),
                    'ok'        => !empty($okeys['zipcode']) ? array($okeys['zipcode']) : array(),
                    'value'     => $contract->zipcode
                ),

                'country' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('personal-field-country'),
                    'errors'    => !empty($errors['country']) ? array($errors['country']) : array(),
                    'ok'        => !empty($okeys['country']) ? array($okeys['country']) : array(),
                    'value'     => $contract->country
                )
            )
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
                            'name'  => 'view-step-entity',
                            'label' => Text::get('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )
        
        )
        
    )

));