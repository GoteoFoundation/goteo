<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Core\View;

$contract = $this['contract'];
$errors = $contract->errors[$this['step']] ?: array();         
$okeys  = $contract->okeys[$this['step']] ?: array();

// coger de la gestión de contratos de dashboard/projects/contract
$secondary_address = empty($contract->post_address) ? false : true;


echo new SuperForm(array(

    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('contract-promoter-main-header'),
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

        'contract_name' => array(
            'type'      => 'textbox',
            'class'     => 'inline',
            'required'  => true,
            'title'     => Text::get('personal-field-contract_name'),
            'errors'    => !empty($errors['contract_name']) ? array($errors['contract_name']) : array(),
            'ok'        => !empty($okeys['contract_name']) ? array($okeys['contract_name']) : array(),
            'value'     => $contract->contract_name
        ),

        'contract_nif' => array(
            'type'      => 'textbox',
            'class'     => 'inline',
            'required'  => true,
            'title'     => Text::get('personal-field-contract_nif'),
            'errors'    => !empty($errors['contract_nif']) ? array($errors['contract_nif']) : array(),
            'ok'        => !empty($okeys['contract_nif']) ? array($okeys['contract_nif']) : array(),
            'value'     => $contract->contract_nif
        ),

        /* Domicilio */
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
            'title'     => Text::get('personal-field-location'),
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
        ),

        'birthdate'  => array(
            'type'      => 'datebox',
            'required'  => true,
            'size'      => 8,
            'title'     => Text::get('personal-field-contract_birthdate'),
            'hint'      => Text::get('tooltip-project-contract_birthdate'),
            'errors'    => !empty($errors['birthdate']) ? array($errors['birthdate']) : array(),
            'ok'        => !empty($okeys['birthdate']) ? array($okeys['birthdate']) : array(),
            'value'     => $contract->birthdate
        ),
        
        'entity' => array(
            'type'      => 'group',
            'title'     => 'Datos de asociación o entidad',
            'children'  => array(
                'office' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => ($contract->type > 0),
                    'title'     => Text::get('personal-field-entity_office'),
                    'hint'      => Text::get('tooltip-contract-entity_office'),
                    'errors'    => !empty($errors['entity_office']) ? array($errors['entity_office']) : array(),
                    'ok'        => !empty($okeys['entity_office']) ? array($okeys['entity_office']) : array(),
                    'value'     => $contract->office
                ),
                
                'entity_name' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => ($contract->type > 0),
                    'title'     => Text::get('personal-field-entity_name'),
                    'hint'      => Text::get('tooltip-contract-entity_name'),
                    'errors'    => !empty($errors['entity_name']) ? array($errors['entity_name']) : array(),
                    'ok'        => !empty($okeys['entity_name']) ? array($okeys['entity_name']) : array(),
                    'value'     => $contract->entity_name
                ),

                'entity_cif' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => ($contract->type > 0),
                    'title'     => Text::get('personal-field-entity_cif'),
                    'hint'      => Text::get('tooltip-contract-entity_cif'),
                    'errors'    => !empty($errors['entity_cif']) ? array($errors['entity_cif']) : array(),
                    'ok'        => !empty($okeys['entity_cif']) ? array($okeys['entity_cif']) : array(),
                    'value'     => $contract->entity_cif
                ),

                'entity_address' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => ($contract->type > 0),
                    'title'     => Text::get('personal-field-address'),
                    'errors'    => !empty($errors['entity_address']) ? array($errors['entity_address']) : array(),
                    'ok'        => !empty($okeys['entity_address']) ? array($okeys['entity_address']) : array(),
                    'value'     => $contract->entity_address
                ),

                'entity_location' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => ($contract->type > 0),
                    'title'     => Text::get('personal-field-location'),
                    'errors'    => !empty($errors['entity_location']) ? array($errors['entity_location']) : array(),
                    'ok'        => !empty($okeys['entity_location']) ? array($okeys['entity_location']) : array(),
                    'value'     => $contract->entity_location
                ),

                'entity_region' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => ($contract->type > 0),
                    'title'     => Text::get('personal-field-region'),
                    'errors'    => !empty($errors['entity_region']) ? array($errors['entity_region']) : array(),
                    'ok'        => !empty($okeys['entity_region']) ? array($okeys['entity_region']) : array(),
                    'value'     => $contract->entity_region
                ),

                'entity_zipcode' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => ($contract->type > 0),
                    'title'     => Text::get('personal-field-zipcode'),
                    'size'      => 7,
                    'errors'    => !empty($errors['entity_zipcode']) ? array($errors['entity_zipcode']) : array(),
                    'ok'        => !empty($okeys['entity_zipcode']) ? array($okeys['entity_zipcode']) : array(),
                    'value'     => $contract->entity_zipcode
                ),

                'entity_country' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => ($contract->type > 0),
                    'title'     => Text::get('personal-field-country'),
                    'errors'    => !empty($errors['entity_country']) ? array($errors['entity_country']) : array(),
                    'ok'        => !empty($okeys['entity_country']) ? array($okeys['entity_country']) : array(),
                    'value'     => $contract->entity_country
                )

            )
        ),

        /* Radio de domicilio postal igual o diferente*/
        /*
        'post_address-radioset' => array(
            'type'      => 'group',
            'class'     => 'inline',
            'title'     => Text::get('personal-field-post_address'),
            'hint'      => Text::get('tooltip-contract-post_address'),
            'children'  => array(
                'post_address-radio-same' =>  array(
                    'name'  => 'secondary_address',
                    'value' => false,
                    'type'  => 'radio',
                    'class' => 'inline',
                    'label' => Text::get('personal-field-post_address-same'),
                    'id'    => 'post_address-radio-same',
                    'checked' => !$contract->secondary_address ? true : false,
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
                    'checked' => $contract->secondary_address ? true : false,
                    'children' => array(
                        // Domicilio postal (a desplegar si es diferente) 
                        'post_address' => array(
                            'type'      => 'textbox',
                            'class'     => 'inline',
                            'title'     => Text::get('personal-field-address'),
                            'rows'      => 6,
                            'cols'      => 40,
                            'hint'      => Text::get('tooltip-contract-post_address'),
                            'errors'    => !empty($errors['post_address']) ? array($errors['post_address']) : array(),
                            'ok'        => !empty($okeys['post_address']) ? array($okeys['post_address']) : array(),
                            'value'     => $contract->post_address
                        ),

                        'post_zipcode' => array(
                            'type'      => 'textbox',
                            'class'     => 'inline',
                            'title'     => Text::get('personal-field-zipcode'),
                            'size'      => 7,
                            'hint'      => Text::get('tooltip-contract-post_address'),
                            'errors'    => !empty($errors['post_zipcode']) ? array($errors['post_zipcode']) : array(),
                            'ok'        => !empty($okeys['post_zipcode']) ? array($okeys['post_zipcode']) : array(),
                            'value'     => $contract->post_zipcode
                        ),

                        'post_location' => array(
                            'type'      => 'textbox',
                            'class'     => 'inline',
                            'title'     => Text::get('personal-field-location'),
                            'size'      => 25,
                            'hint'      => Text::get('tooltip-contract-post_address'),
                            'errors'    => !empty($errors['post_location']) ? array($errors['post_location']) : array(),
                            'ok'        => !empty($okeys['post_location']) ? array($okeys['post_location']) : array(),
                            'value'     => $contract->post_location
                        ),

                        'post_country' => array(
                            'type'      => 'textbox',
                            'class'     => 'inline',
                            'title'     => Text::get('personal-field-country'),
                            'size'      => 25,
                            'hint'      => Text::get('tooltip-contract-post_address'),
                            'errors'    => !empty($errors['post_country']) ? array($errors['post_country']) : array(),
                            'ok'        => !empty($okeys['post_country']) ? array($okeys['post_country']) : array(),
                            'value'     => $contract->post_country
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
                            'name'  => 'view-step-accounts',
                            'label' => Text::get('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )
        
        )
        
    )

));