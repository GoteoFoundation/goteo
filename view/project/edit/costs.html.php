<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Library\View;
            
$project = $this['project'];

$costsTypes = array();

foreach ($this['types'] as $id => $type) {
    $costTypes[] = array(
        'value'     => $id,
        'label'     => $type,
        'summary'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque luctus ligula vel leo hendrerit sed consequat nulla.',
        'class'     => $id
    );
}


$costs = array();

if (!empty($project->costs)) {
    foreach ($project->costs as $cost) {     
        
        $costs["cost-{$cost->id}"] = array(
            'type'      => 'group',      
            'class'     => 'cost',
            'children'  => array(                         
                "cost-{$cost->id}-cost" => array(
                    'title'     => 'Coste',
                    'type'      => 'textbox',
                    'size'      => 100,
                    'class'     => 'inline',
                    'value'     => $cost->cost,
                    'hint'      => Text::get('tooltip-project-cost-cost'),
                ),
                "cost-{$cost->id}-type" => array(
                    'title'     => 'Tipo',
                    'class'     => 'inline cost-type',
                    'type'      => 'radios',
                    'options'   => $costTypes,
                    'value'     => $cost->type,
                    'hint'      => Text::get('tooltip-project-cost-type'),
                ),
                "cost-{$cost->id}-description" => array(
                    'type'      => 'textarea',
                    'title'     => 'Descripción',
                    'cols'      => 100,
                    'rows'      => 4,
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-project-cost-description'),
                    'value'     => $cost->description
                ),                                       
                "cost-{$cost->id}-dates" => array(
                    'type'      => 'group',
                    'title'     => 'Fechas',
                    'class'     => 'inline cost-dates',
                    'hint'      => Text::get('tooltip-project-cost-dates'),
                    'children'  => array(
                        "cost-{$cost->id}-from"  => array(
                            'class'     => 'inline cost-from',
                            'type'      => 'datebox',
                            'size'      => 8,
                            'title'     => 'Desde',
                            'value'     => $cost->from
                        ),
                        "cost-{$cost->id}-until"  => array(
                            'class'     => 'inline cost-until',
                            'title'     => 'Hasta',
                            'type'      => 'datebox',
                            'size'      => 8,
                            'value'     => $cost->until
                        )
                    )
                ),        
                "cost-{$cost->id}-amount" => array(
                    'type'      => 'textbox',
                    'title'     => 'Valor',
                    'size'      => 8,
                    'class'     => 'inline cost-amount',
                    'value'     => $cost->amount,
                    'hint'      => Text::get('tooltip-project-cost-amount'),
                    'children'  => array(
                        "cost-{$cost->id}-required"  => array(
                            'type'      => 'checkbox',
                            'class'     => 'inline cost-required',
                            'value'     => 1,
                            'label'     => 'Imprescindible',
                            'checked'   => $cost->required,
                        )
                    )
                ),
                "cost-{$cost->id}-remove" => array(
                    'type'  => 'submit',
                    'label' => 'Eliminar',
                    'class' => 'inline remove'
                )
            )
        );
    }    
}
/*
$costs['ncost'] = array(
    
    'type'      => 'group',
    'title'     => 'Nuevo coste',
    'class'     => 'cost cost-new',
    'children'  => array(
        'ncost' => array(
            'title'     => 'Coste',
            'type'      => 'textbox',
            'size'      => 100,
            'class'     => 'inline',
            'value'     => $cost->cost,
            'hint'      => Text::get('tooltip-project-cost-cost')
        ),
        'ncost-type'    => array(
            'title'     => 'Tipo',
            'class'     => 'inline cost-type',
            'type'      => 'radios',
            'options'   => $costTypes,
            'hint'      => Text::get('tooltip-project-cost-type')
        ),
        'ncost-description' => array(
            'type'      => 'textarea',
            'title'     => 'Descripción',
            'cols'      => 100,
            'rows'      => 4,
            'class'     => 'inline',
            'value'     => $cost->description,
            'hint'      => Text::get('tooltip-project-cost-description')
        ),        
        'ncost-dates' => array(
            'type'      => 'group',
            'title'     => 'Fechas',
            'class'     => 'inline cost-dates',
            'hint'      => Text::get('tooltip-project-cost-dates'),
            'children'  => array(
                'ncost-from'  => array(
                    'class'     => 'cost-from inline',
                    // 'type'      => 'date',
                    'type'      => 'textbox',
                    'size'      => 8,
                    'title'     => 'Desde',
                    'value'     => $cost->from                    
                ),
                'ncost-to'  => array(
                    'class'     => 'cost-to inline',
                    'title'     => 'Hasta',
                    'type'      => 'textbox',
                    'size'      => 8,
                    'value'     => $cost->until
                )
            )
        ),
        'ncost-amount' => array(
            'type'      => 'textbox',
            'title'     => 'Valor',
            'size'      => 8,
            'class'     => 'inline cost-amount',
            'value'     => $cost->amount,
            'hint'      => Text::get('tooltip-project-cost-amount'),
            'children'  => array(
                'ncost-required'  => array(
                    'type'      => 'checkbox',
                    // 'type'      => 'date',
                    'class'     => 'inline cost-required',
                    //'title'     => 'Imprescindible',                    
                    'value'     => 1,
                    'label'     => 'Imprescindible',
                    'checked'   => $cost->required,
                    'hint'      => Text::get('tooltip-project-cost-required')
                )
            )
        ),
        'ncost-add' => array(
            'type'  => 'submit',
            'label' => 'Añadir',
            'class' => 'add',
        )
    )    
    
);
*/
echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => 'Proyecto/Costes',
    'hint'          => Text::get('guide-project-costs'),    
    'class'         => 'aqua',
    'footer'        => array(
        'view-step-rewards' => array(
            'name'  => 'view-step-rewards',
            'type'  => 'submit',
            'label' => 'Siguiente',
            'class' => 'next'
        )        
    ),    
    'elements'      => array(
        'view-step-costs' => array(
            'type'  => 'hidden',
            'value' => '1'
        ),
        'costs' => array(
            'type'      => 'group',
            'title'     => 'Desglose de costes',
            'hint'      => Text::get('tooltip-project-costs'),
            'children'  => $costs  + array(
                'cost-add' => array(
                    'type'  => 'submit',
                    'label' => 'Añadir',
                    'class' => 'add',
                )
            )
        ),
        
        'resource' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'title'     => 'Otros recursos',
            'hint'      => Text::get('tooltip-project-resource'),
            'value'     => $project->resource
        )/*,          
        
        'schedule' => array(                        
            'title'     => 'Agenda',            
            'class'     => 'fullwidth'
        ), */         
    )

));