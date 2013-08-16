<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Core\View;

$contract = $this['contract'];
$errors = $contract->errors[$this['step']] ?: array();         
$okeys  = $contract->okeys[$this['step']] ?: array();

$hint = ($contract->type == 0) ? Text::get('guide-contract-no_entity') : Text::get('guide-contract-entity');

// captions segun tipo (tambien campos segun tipo
switch ($contract->type) {
    case 0:
        // propio nombre y derecho
        $reg_name = array (
            'type' => 'hidden',
            'value' => ''
        );
        $reg_date = array (
            'type' => 'hidden',
            'value' => ''
        );
        $reg_number = array (
            'type' => 'hidden',
            'value' => ''
        );
        $reg_id = array (
            'type' => 'hidden',
            'value' => ''
        );
        break;
    
    case 1:
        // representatne de asociación
        $reg_name = array(
                    'type'      => 'textbox',
                    'title'     => Text::get('contract-field-reg_name_1'),
                    'required'  => true,
                    'value'     => $contract->reg_name,
                    'errors'    => !empty($errors['reg_name']) ? array($errors['reg_name']) : array(),
                    'ok'        => !empty($okeys['reg_name']) ? array($okeys['reg_name']) : array()
                );

        $reg_date = array (
            'type' => 'hidden',
            'value' => ''
        );
        
        $reg_number = array(
                    'type'      => 'textbox',
                    'title'     => Text::get('contract-field-reg_number_1'),
                    'required'  => true,
                    'value'     => $contract->reg_number,
                    'errors'    => !empty($errors['reg_number']) ? array($errors['reg_number']) : array(),
                    'ok'        => !empty($okeys['reg_number']) ? array($okeys['reg_number']) : array()
                );

        $reg_id = array (
            'type' => 'hidden',
            'value' => ''
        );
        break;
    
    case 2:
        // apoderado de entidad mercantil
        $reg_name = array(
                    'type'      => 'textbox',
                    'title'     => Text::get('contract-field-reg_name_2'),
                    'required'  => true,
                    'value'     => $contract->reg_name,
                    'errors'    => !empty($errors['reg_name']) ? array($errors['reg_name']) : array(),
                    'ok'        => !empty($okeys['reg_name']) ? array($okeys['reg_name']) : array()
                );

        $reg_date  = array(
            'type'      => 'datebox',
            'required'  => true,
            'size'      => 8,
            'title'     => Text::get('contract-field-reg_date_2'),
            'errors'    => !empty($errors['reg_date']) ? array($errors['reg_date']) : array(),
            'ok'        => !empty($okeys['reg_date']) ? array($okeys['reg_date']) : array(),
            'value'     => $contract->reg_date
        );
                
        
        
        $reg_number = array(
                    'type'      => 'textbox',
                    'title'     => Text::get('contract-field-reg_number_2'),
                    'required'  => true,
                    'value'     => $contract->reg_number,
                    'errors'    => !empty($errors['reg_number']) ? array($errors['reg_number']) : array(),
                    'ok'        => !empty($okeys['reg_number']) ? array($okeys['reg_number']) : array()
                );

        $reg_id = array(
                    'type'      => 'textbox',
                    'title'     => Text::get('contract-field-reg_id_2'),
                    'required'  => true,
                    'value'     => $contract->reg_id,
                    'errors'    => !empty($errors['reg_id']) ? array($errors['reg_id']) : array(),
                    'ok'        => !empty($okeys['reg_id']) ? array($okeys['reg_id']) : array()
                );
        break;
}

// Datos de asociación (si representante) o entidad (si apoderado)
$elements = ($contract->type == 0) 
    // si es en su propio nombre
    ? array(
            'process_entity' => array (
                'type' => 'hidden',
                'value' => 'entity'
            ),

            'reg_name' => $reg_name,

            'reg_date' => $reg_date,

            'reg_number' => $reg_number,

            'reg_id' => $reg_id,


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

    // si es un representatne
    : array(
        'process_entity' => array (
            'type' => 'hidden',
            'value' => 'entity'
        ),
        
        'entity_name' => array(
            'type'      => 'textbox',
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

        /* cargo */
        'office' => array(
            'type'      => 'textbox',
            'required'  => ($contract->type > 0),
            'title'     => Text::get('personal-field-entity_office'),
            'hint'      => Text::get('tooltip-contract-entity_office'),
            'errors'    => !empty($errors['office']) ? array($errors['office']) : array(),
            'ok'        => !empty($okeys['office']) ? array($okeys['office']) : array(),
            'value'     => $contract->office
        ),

        /* Domicilio */
        'entity' => array(
            'type'      => 'group',
            'title'     => Text::get('personal-field-post_address'),
            'children'  => array(

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
                    'title'     => Text::get('contract-field-location'),
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

        'reg_name' => $reg_name,
        
        'reg_date' => $reg_date,
        
        'reg_number' => $reg_number,
        
        'reg_id' => $reg_id,
        
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
;
    
    
echo new SuperForm(array(

    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('contract-step-entity'),
    'hint'          => $hint,    
    'elements'      => $elements
    )
);