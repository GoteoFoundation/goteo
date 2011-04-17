<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Library\View;
            
$project = $this['project'];

$costs = null;

if (!empty($project->costs)) {
    $costs = array();
    foreach ($project->costs as $cost) {     
        $costs["cost-{$cost->id}"] = array(
            'type'      => 'group',            
            'children'  => array(                         
                'summary' => array(
                    'title'     => 'Coste',
                    'type'      => 'textbox',
                    'size'      => 100,
                    'inline'    => true,
                    'value'     => ''
                ),
                'description' => array(
                    'type'      => 'textarea',
                    'title'     => 'Descripción',
                    'cols'      => 100,
                    'rows'      => 4,
                    'inline'    => true,
                    'value'     => ''
                ),
                'amount' => array(
                    'type'      => 'textbox',
                    'title'     => 'Valor',
                    'size'      => 8,
                    'inline'    => true,
                    'value'     => ''
                ),
                'from'  => array(
                    'inline'    => true,
                    // 'type'      => 'date',
                    'title'     => 'Desde',
                ),
                'to'  => array(
                    'inline'    => true,
                    'title'     => 'Hasta',
                ),
                'required'  => array(
                    'type'      => 'checkbox',
                    // 'type'      => 'date',
                    'inline'    => true,
                    'title'     => 'Imprescindible',
                )
            )
        );
    }    
}


echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Proyecto/Costes',
    'hint'          => Text::get('guide-project-costs'),    
    'class'         => 'red',
    'footer'        => array(
        'view-step-rewards' => array(
            'type'  => 'submit',
            'label' => 'Siguiente',
            'class' => 'next'
        )        
    ),    
    'elements'      => array(
        
        'costs' => array(
            'type'      => 'group',
            'title'     => 'Desglose de costes',
            'hint'      => Text::get('tooltip-project-costs'),
            'children'  => $costs
        ),
        
        'resource' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'title'     => 'Otros recursos',
            'hint'      => Text::get('tooltip-project-resource'),
            'value'     => $project->resource
        ),          
        
        'schedule' => array(                        
            'title'     => 'Agenda',            
            'class'     => 'fullwidth'
        ),          
    )

));