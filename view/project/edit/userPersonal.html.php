<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $this['project'];
$errors = $project->errors[$this['step']] ?: array();         

echo new SuperForm(array(

    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('personal-main-header'),
    'hint'          => Text::get('guide-project-contract-information'),    
    'footer'        => array(
        'view-step-overview' => array(
            'type'  => 'submit',
            'label' => Text::get('form-next-button'),
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
            'title'     => Text::get('personal-field-contract_name'),
            'hint'      => Text::get('tooltip-project-contract_name'),
            'errors'    => !empty($errors['contract_name']) ? array($errors['contract_name']) : array(),
            'value'     => $project->contract_name
        ),
        
        'contract_nif' => array(
            'type'      => 'textbox',
            'required'  => true,
            'title'     => Text::get('personal-field-contract_nif'),
            'size'      => 9,
            'hint'      => Text::get('tooltip-project-contract_nif'),
            'errors'    => !empty($errors['contract_nif']) ? array($errors['contract_nif']) : array(),
            'value'     => $project->contract_nif
        ),
        
        'phone' => array(
            'type'  => 'textbox',
            'required'  => true,
            'title' => Text::get('personal-field-phone'),
            'dize'  => 15,
            'hint'  => Text::get('tooltip-project-phone'),
            'errors'    => !empty($errors['phone']) ? array($errors['phone']) : array(),
            'value' => $project->phone
        ),
        
        'address' => array(
            'type'  => 'textbox',
            'required'  => true,
            'title' => Text::get('personal-field-address'),
            'rows'  => 6,
            'cols'  => 40,
            'hint'  => Text::get('tooltip-project-address'),
            'errors'    => !empty($errors['address']) ? array($errors['address']) : array(),
            'value' => $project->address
        ),
        
        'zipcode' => array(
            'type'  => 'textbox',
            'required'  => true,
            'title' => Text::get('personal-field-zipcode'),
            'size'  => 7,
            'hint'  => Text::get('tooltip-project-zipcode'),
            'errors'    => !empty($errors['zipcode']) ? array($errors['zipcode']) : array(),
            'value' => $project->zipcode
        ),
        
        'location' => array(
            'type'  => 'textbox',
            'required'  => true,
            'title' => Text::get('personal-field-location'),
            'size'  => 25,
            'hint'  => Text::get('tooltip-project-location'),
            'errors'    => !empty($errors['location']) ? array($errors['location']) : array(),
            'value' => $project->location
        ),
        
        'country' => array(
            'type'  => 'textbox',
            'required'  => true,
            'title' => Text::get('personal-field-country'),
            'size'  => 25,
            'hint'  => Text::get('tooltip-project-country'),
            'errors'    => !empty($errors['country']) ? array($errors['country']) : array(),
            'value' => $project->country
        ),
        
    )

));