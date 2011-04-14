<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            
$project = $this['project'];

echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Proyecto/Previsualización',
    'hint'          => Text::get('guide-project-preview'),    
    'class'         => 'aqua',
    'footer'        => array(
        array(
            'type'  => 'submit',
            'label' => 'Revisar',
            'class' => 'retry'
        ),
        array(
            'type'  => 'submit',
            'label' => 'Enviar',
            'class' => 'confirm'
        )
    ),    
    'elements'      => array(
        
        'preview' => array(
            'type'      => 'group'
        )
        
    )

));