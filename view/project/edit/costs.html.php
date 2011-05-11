<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Core\View;
            
$project = $this['project'];

$costsTypes = array();

foreach ($this['types'] as $id => $type) {
    $costTypes[] = array(
        'value'     => $id,
        'label'     => $type,
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
                    'class'     => 'inline cost-description',
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
                    'label' => 'Quitar',
                    'class' => 'inline remove'
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
        'process_costs' => array (
            'type' => 'hidden',
            'value' => 'costs'
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
        
        'cost-meter' => array(
            'title'     => 'Totales',
            'class'     => 'cost-meter',
            'view'      => new View('view/project/edit/costs/meter.html.php', array(
                'project'   => $project
            )),
            'hint'      => Text::get('tooltip-project-totals')
        ),
        
        'resource' => array(
            'type'      => 'textarea',
            'cols'      => 40,
            'rows'      => 4,
            'title'     => 'Otros recursos',
            'hint'      => Text::get('tooltip-project-resource'),
            'value'     => $project->resource
        )/*
        
        'schedule' => array(                        
            'title'     => 'Agenda',            
            'class'     => 'fullwidth'
        ), */         
    )

));