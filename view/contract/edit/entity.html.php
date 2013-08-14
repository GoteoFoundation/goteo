<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Core\View;

$contract = $this['contract'];
$errors = $contract->errors[$this['step']] ?: array();         
$okeys  = $contract->okeys[$this['step']] ?: array();

$hint = ($contract->type == 0) 
? 'Como promotor en propio nombre y derecho no tienes nada para rellenar en este paso.'
: Text::get('guide-contract-entity');

// Datos de asociación (si representante) o entidad (si apoderado)
$elements = ($contract->type == 0) 
    // si es en su propio nombre
    ? array(
            'process_entity' => array (
                'type' => 'hidden',
                'value' => 'entity'
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

        /* cargo */
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

        /* Domicilio */
        'entity' => array(
            'type'      => 'group',
            'title'     => 'Dirección social',
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
                    'title'     => 'Municipio',
                    'errors'    => !empty($errors['entity_location']) ? array($errors['entity_location']) : array(),
                    'ok'        => !empty($okeys['entity_location']) ? array($okeys['entity_location']) : array(),
                    'value'     => $contract->entity_location
                ),

                'entity_region' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => ($contract->type > 0),
                    'title'     => 'Provincia',
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
    'title'         => Text::get('contract-entity-main-header'),
    'hint'          => $hint,    
    'elements'      => $elements
    )
);