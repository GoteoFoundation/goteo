<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$contract = $this['contract'];
$errors = $contract->errors[$this['step']] ?: array();
$okeys  = $contract->okeys[$this['step']] ?: array();


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
                    'title'     => 'Registro en el que se inscribió la asociación',
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
                    'title'     => 'Número de Registro',
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
                    'title'     => 'Nombre del notario que otorgó la escritura pública de la empresa',
                    'required'  => true,
                    'value'     => $contract->reg_name,
                    'errors'    => !empty($errors['reg_name']) ? array($errors['reg_name']) : array(),
                    'ok'        => !empty($okeys['reg_name']) ? array($okeys['reg_name']) : array()
                );

        $reg_date  = array(
            'type'      => 'datebox',
            'required'  => true,
            'size'      => 8,
            'title'     => 'Fecha en que se otorgó la escritura pública de la empresa',
            'errors'    => !empty($errors['reg_date']) ? array($errors['reg_date']) : array(),
            'ok'        => !empty($okeys['reg_date']) ? array($okeys['reg_date']) : array(),
            'value'     => $contract->reg_date
        );
                
        
        
        $reg_number = array(
                    'type'      => 'textbox',
                    'title'     => 'Número del protocolo del notario',
                    'required'  => true,
                    'value'     => $contract->reg_number,
                    'errors'    => !empty($errors['reg_number']) ? array($errors['reg_number']) : array(),
                    'ok'        => !empty($okeys['reg_number']) ? array($okeys['reg_number']) : array()
                );

        $reg_id = array(
                    'type'      => 'textbox',
                    'title'     => 'Inscripción en el Registro Mercantil de (ciudad y número de registro)',
                    'required'  => true,
                    'value'     => $contract->reg_id,
                    'errors'    => !empty($errors['reg_id']) ? array($errors['reg_id']) : array(),
                    'ok'        => !empty($okeys['reg_id']) ? array($okeys['reg_id']) : array()
                );
        break;
}

$superform = array(
    'level'         => $this['level'],
    'action'        => '',
    'method'        => 'post',
    'title'         => Text::get('contract-additional-main-header'),
    'hint'          => Text::get('guide-contract-additional'),
    'class'         => 'aqua',        
    'elements'      => array(
        'process_additional' => array (
            'type' => 'hidden',
            'value' => 'additional'
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
                            'name'  => 'view-step-final',
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