<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Core\View;

$contract = $vars['contract'];
$errors = $contract->errors[$vars['step']] ?: array();
$okeys  = $contract->okeys[$vars['step']] ?: array();

$hint = ($contract->type == 0) ? Text::get('guide-contract-no_entity') : Text::get('guide-contract-entity');

// captions segun tipo (tambien campos segun tipo
switch ($contract->type) {
    case 1:
        // como representante de asociación
        $regfields = array(
            'reg_name' => array(
                    'type'      => 'textbox',
                    'title'     => Text::get('contract-field-reg_name_1'),
                    'required'  => true,
                    'value'     => $contract->reg_name,
                    'errors'    => !empty($errors['reg_name']) ? array($errors['reg_name']) : array(),
                    'ok'        => !empty($okeys['reg_name']) ? array($okeys['reg_name']) : array()
                ),

            'reg_date' => array (
                'type' => 'hidden',
                'value' => ''
            ),

            'reg_number' => array(
                'type'      => 'textbox',
                'title'     => Text::get('contract-field-reg_number_1'),
                'required'  => true,
                'value'     => $contract->reg_number,
                'errors'    => !empty($errors['reg_number']) ? array($errors['reg_number']) : array(),
                'ok'        => !empty($okeys['reg_number']) ? array($okeys['reg_number']) : array()
            ),

            'reg_id' => array (
                'type' => 'hidden',
                'value' => ''
            ),

            'reg_idloc' => array (
                'type' => 'hidden',
                'value' => ''
            ),

            'reg_idname' => array (
                'type' => 'hidden',
                'value' => ''
            )
        );
        break;

    case 2:
        // como representante de de entidad mercantil
        $regfields = array(
            'reg_name' => array(
                    'type'      => 'textbox',
                    'title'     => Text::get('contract-field-reg_name_2'),
                    'required'  => true,
                    'value'     => $contract->reg_name,
                    'errors'    => !empty($errors['reg_name']) ? array($errors['reg_name']) : array(),
                    'ok'        => !empty($okeys['reg_name']) ? array($okeys['reg_name']) : array()
                ),

            'reg_number' => array(
                    'type'      => 'textbox',
                    'title'     => Text::get('contract-field-reg_number_2'),
                    'required'  => true,
                    'value'     => $contract->reg_number,
                    'errors'    => !empty($errors['reg_number']) ? array($errors['reg_number']) : array(),
                    'ok'        => !empty($okeys['reg_number']) ? array($okeys['reg_number']) : array()
                ),

            'reg_idname' => array(
                    'type'      => 'textbox',
                    'title'     => Text::get('contract-field-reg_idname_2'),
                    'required'  => true,
                    'value'     => $contract->reg_idname,
                    'errors'    => !empty($errors['reg_idname']) ? array($errors['reg_idname']) : array(),
                    'ok'        => !empty($okeys['reg_idname']) ? array($okeys['reg_idname']) : array()
                ),

            'reg_id' => array(
                    'type'      => 'textbox',
                    'title'     => Text::get('contract-field-reg_id_2'),
                    'required'  => true,
                    'value'     => $contract->reg_id,
                    'errors'    => !empty($errors['reg_id']) ? array($errors['reg_id']) : array(),
                    'ok'        => !empty($okeys['reg_id']) ? array($okeys['reg_id']) : array()
                ),

            'reg_idloc' => array(
                'type'      => 'textbox',
                'title'     => Text::get('contract-field-reg_idloc_2'),
                'required'  => true,
                'value'     => $contract->reg_idloc,
                'errors'    => !empty($errors['reg_idloc']) ? array($errors['reg_idloc']) : array(),
                'ok'        => !empty($okeys['reg_idloc']) ? array($okeys['reg_idloc']) : array()
                ),

            'reg_date'  => array(
                'type'      => 'datebox',
                'required'  => true,
                'size'      => 8,
                'title'     => Text::get('contract-field-reg_date_2'),
                'errors'    => !empty($errors['reg_date']) ? array($errors['reg_date']) : array(),
                'ok'        => !empty($okeys['reg_date']) ? array($okeys['reg_date']) : array(),
                'value'     => $contract->reg_date
                )
        );
        break;
}


// Datos de asociación (si representante) o entidad (si apoderado)
if ($contract->type == 0) {
    // si es en su propio nombre
    $elements = array(
            'process_entity' => array (
                'type' => 'hidden',
                'value' => 'entity'
            ),

            'reg_name' => array (
                'type' => 'hidden',
                'value' => ''
            ),
            'reg_date' => array (
                'type' => 'hidden',
                'value' => ''
            ),
            'reg_number' => array (
                'type' => 'hidden',
                'value' => ''
            ),
            'reg_id' => array (
                'type' => 'hidden',
                'value' => ''
            ),
            'reg_idloc' => array (
                'type' => 'hidden',
                'value' => ''
            ),
            'reg_idname' => array (
                'type' => 'hidden',
                'value' => ''
            ),

            'footer' => array(
                'type'      => 'group',
                'children'  => array(
                    'errors' => array(
                        'title' => Text::get('form-footer-errors_title'),
                        'view'  => new View('contract/edit/errors.html.php', array(
                            'contract'   => $contract,
                            'step'      => $vars['step']
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

        );

} else {
    // si es un representatne
    $elements = array(
        'process_entity' => array (
            'type' => 'hidden',
            'value' => 'entity'
        ),

        'entity_name' => array(
            'type'      => 'textbox',
            'required'  => true,
            'title'     => Text::get('personal-field-entity_name'),
            'hint'      => Text::get('tooltip-contract-entity_name'),
            'errors'    => !empty($errors['entity_name']) ? array($errors['entity_name']) : array(),
            'ok'        => !empty($okeys['entity_name']) ? array($okeys['entity_name']) : array(),
            'value'     => $contract->entity_name
        ),

        'entity_cif' => array(
            'type'      => 'textbox',
            'class'     => 'inline',
            'required'  => true,
            'title'     => Text::get('personal-field-entity_cif'),
            'hint'      => Text::get('tooltip-contract-entity_cif'),
            'errors'    => !empty($errors['entity_cif']) ? array($errors['entity_cif']) : array(),
            'ok'        => !empty($okeys['entity_cif']) ? array($okeys['entity_cif']) : array(),
            'value'     => $contract->entity_cif
        ),

        /* cargo */
        'office' => array(
            'type'      => 'textbox',
            'required'  => true,
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
                    'required'  => true,
                    'title'     => Text::get('personal-field-address'),
                    'errors'    => !empty($errors['entity_address']) ? array($errors['entity_address']) : array(),
                    'ok'        => !empty($okeys['entity_address']) ? array($okeys['entity_address']) : array(),
                    'value'     => $contract->entity_address
                ),

                'entity_location' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('contract-field-location'),
                    'errors'    => !empty($errors['entity_location']) ? array($errors['entity_location']) : array(),
                    'ok'        => !empty($okeys['entity_location']) ? array($okeys['entity_location']) : array(),
                    'value'     => $contract->entity_location
                ),

                'entity_region' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('personal-field-region'),
                    'errors'    => !empty($errors['entity_region']) ? array($errors['entity_region']) : array(),
                    'ok'        => !empty($okeys['entity_region']) ? array($okeys['entity_region']) : array(),
                    'value'     => $contract->entity_region
                ),

                'entity_zipcode' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('personal-field-zipcode'),
                    'size'      => 7,
                    'errors'    => !empty($errors['entity_zipcode']) ? array($errors['entity_zipcode']) : array(),
                    'ok'        => !empty($okeys['entity_zipcode']) ? array($okeys['entity_zipcode']) : array(),
                    'value'     => $contract->entity_zipcode
                ),

                'entity_country' => array(
                    'type'      => 'textbox',
                    'class'     => 'inline',
                    'required'  => true,
                    'title'     => Text::get('personal-field-country'),
                    'errors'    => !empty($errors['entity_country']) ? array($errors['entity_country']) : array(),
                    'ok'        => !empty($okeys['entity_country']) ? array($okeys['entity_country']) : array(),
                    'value'     => $contract->entity_country
                )

            )
        ),

        /* Registro */
        'regdata' => array(
            'type'      => 'group',
            'title'     => Text::get('contract-field-regdata'),
            'children'  => $regfields
        ),

        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'errors' => array(
                    'title' => Text::get('form-footer-errors_title'),
                    'view'  => new View('contract/edit/errors.html.php', array(
                        'contract'   => $contract,
                        'step'      => $vars['step']
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

    );
}


echo SuperForm::get(array(

    'level'         => $vars['level'],
    'method'        => 'post',
    'title'         => Text::get('contract-step-entity'),
    'hint'          => $hint,
    'elements'      => $elements
    )
);
