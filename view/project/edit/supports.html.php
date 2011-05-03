<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            
$project = $this['project'];

$support_types = $this['types'];

$supports = array();

foreach ($project->supports as $support) {
    
    $supports["support-{$support->id}"] = array(
            'type'      => 'group',      
            'class'     => 'support',
            'children'  => array(                         
                "support-{$support->id}-support" => array(
                    'title'     => 'Resumen',
                    'type'      => 'textbox',
                    'size'      => 100,
                    'class'     => 'inline',
                    'value'     => $support->support,
                    'hint'      => Text::get('tooltip-project-support-support'),
                ),
                "support-{$support->id}-type" => array(
                    'title'     => 'Tipo',
                    'class'     => 'inline support-type',
                    'type'      => 'radios',
                    'options'   => $support_types,
                    'value'     => $support->type,
                    'hint'      => Text::get('tooltip-project-support-type'),
                ),
                "support-{$support->id}-description" => array(
                    'type'      => 'textarea',
                    'title'     => 'Descripción',
                    'cols'      => 100,
                    'rows'      => 4,
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-project-support-description'),
                    'value'     => $support->description
                ),
                "support-{$support->id}-remove" => array(
                    'type'  => 'submit',
                    'label' => 'Quitar',
                    'class' => 'inline remove support-remove'
                )
            )
        );
}

echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Proyecto/Colaboraciones',
    'hint'          => Text::get('guide-project-supports'),    
    'class'         => 'aqua',
    'footer'        => array(                        
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'view-step-preview',
            'label' => 'Siguiente',
            'class' => 'next'
        )        
    ),    
    'elements'      => array(        
        'suppports' => array(
            'type'      => 'group',
            'title'     => 'Colaboraciones',
            'hint'      => Text::get('tooltip-project-supports'),
            'children'  => $supports + array(
                'support-add' => array(
                    'type'  => 'submit',
                    'label' => 'Añadir',
                    'class' => 'add support-add',
                )
            )
        )        
    )

));