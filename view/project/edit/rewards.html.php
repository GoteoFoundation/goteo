<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            
$project = $this['project'];

echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Proyecto/Retornos',
    'hint'          => Text::get('guide-project-rewards'),    
    'class'         => 'aqua',
    'footer'        => array(
        'view-step-supports' => array(
            'type'  => 'submit',
            'name'  => 'view-step-supports',
            'label' => 'Siguiente',
            'class' => 'next'
        )        
    ),    
    'elements'      => array(
        
        'social' => array(
            'type'      => 'group',
            'title'     => 'Retornos colectivos',
            'hint'      => Text::get('tooltip-project-social_reward'),
        ),
        
        'nsocial' => array(
            'type'      => 'group',
            'title'     => 'Recompensas individuales',
            'hint'      => Text::get('tooltip-project-individual_reward'),
        ),          
        
        'schedule' => array(                        
            'title'     => 'Agenda',            
            'hint'      => Text::get('tooltip-project-schedule'),
        ),          
    )

));