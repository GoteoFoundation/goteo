<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $this['project'];
$errors = $project->errors[$this['step']] ?: array();         

echo new SuperForm(array(

    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Usuario/Datos personales',
    'hint'          => Text::get('guide-project-contract-information'),    
    'footer'        => array(
        'view-step-overview' => array(
            'type'  => 'submit',
            'label' => 'Siguiente',
            'class' => 'next',
            'name'  => 'view-step-overview'
        )        
    ),
    'elements'      => array(
        'process_userPersonal' => array (
            'type' => 'hidden',
            'value' => 'userPersonal'
        ),
        
        'contract_name' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => 'Nombre',
            'hint'      => Text::get('tooltip-project-contract_name'),
            'errors'    => !empty($errors['contract_name']) ? array($errors['contract_name']) : array(),
            'value'     => $project->contract_name
        ),
        
        'contract_surname' => array(
            'type'      => 'textbox',
            'title'     => 'Apellidos',
            'size'      => 30,
            'required'  => true,
            'hint'       => Text::get('tooltip-project-contract_surname'),
            'errors'    => !empty($errors['contract_surname']) ? array($errors['contract_surname']) : array(),
            'value'     => $project->contract_surname
        ),
        
        'contract_nif' => array(
            'type'      => 'textbox',
            'required'  => true,
            'title'     => 'NIF',
            'size'      => 9,
            'hint'      => Text::get('tooltip-project-contract_nif'),
            'errors'    => !empty($errors['contract_nif']) ? array($errors['contract_nif']) : array(),
            'value'     => $project->contract_nif
        ),
        
        'contract_email' => array(
            'type'      => 'textbox',
            'title'     => 'E-mail',
            'required'  => true,
            'size'      => 40,
            'hint'      => Text::get('tooltip-project-contract_email'),
            'errors'    => !empty($errors['contract_email']) ? array($errors['contract_email']) : array(),
            'value'     => $project->contract_email
        ),
        
        'phone' => array(
            'type'  => 'textbox',
            'title' => 'Teléfono',            
            'dize'  => 15,
            'hint'  => Text::get('tooltip-project-phone'),
            'errors'    => !empty($errors['phone']) ? array($errors['phone']) : array(),
            'value' => $project->phone
        ),
        
        'address' => array(
            'type'  => 'textarea',
            'title' => 'Dirección',  
            'rows'  => 6,
            'cols'  => 40,
            'hint'  => Text::get('tooltip-project-address'),
            'errors'    => !empty($errors['address']) ? array($errors['address']) : array(),
            'value' => $project->address
        ),
        
        'zipcode' => array(
            'type'  => 'textbox',
            'title' => 'Código postal',            
            'size'  => 7,
            'hint'  => Text::get('tooltip-project-zipcode'),
            'errors'    => !empty($errors['zipcode']) ? array($errors['zipcode']) : array(),
            'value' => $project->zipcode
        ),
        
        'location' => array(
            'type'  => 'textbox',
            'title' => 'Lugar de residencia',            
            'size'  => 25,
            'hint'  => Text::get('tooltip-project-location'),
            'errors'    => !empty($errors['location']) ? array($errors['location']) : array(),
            'value' => $project->location
        ),
        
        'country' => array(
            'type'  => 'textbox',
            'title' => 'País',
            'size'  => 25,
            'hint'  => Text::get('tooltip-project-country'),
            'errors'    => !empty($errors['country']) ? array($errors['country']) : array(),
            'value' => $project->country
        ),
        
    )

));