<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            
$project = $this['project'];

echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Proyecto/Colaboraciones',
    'hint'          => Text::get('guide-project-rewards'),    
    'class'         => 'aqua',
    'footer'        => array(                        
        'view-step-preview' => array(
            'type'  => 'submit',
            'label' => 'Siguiente',
            'class' => 'next'
        )        
    ),    
    'elements'      => array(
        
        'social' => array(
            'type'      => 'group',
            'title'     => 'Colaboraciones',
            'hint'      => Text::get('tooltip-project-supports'),
        )
        
    )

));