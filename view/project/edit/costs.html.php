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
                    'title'     => Text::get('costs-field-cost'),
//                    'required'  => true,
                    'type'      => 'textbox',
                    'size'      => 100,
                    'class'     => 'inline',
                    'value'     => $cost->cost,
                    'hint'      => Text::get('tooltip-project-cost-cost'),
                ),
                "cost-{$cost->id}-type" => array(
                    'title'     => Text::get('costs-field-type'),
//                    'required'  => true,
                    'class'     => 'inline cost-type',
                    'type'      => 'radios',
                    'options'   => $costTypes,
                    'value'     => $cost->type,
                    'hint'      => Text::get('tooltip-project-cost-type'),
                ),
                "cost-{$cost->id}-description" => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('costs-field-description'),
                    'cols'      => 100,
                    'rows'      => 4,
                    'class'     => 'inline cost-description',
                    'hint'      => Text::get('tooltip-project-cost-description'),
                    'value'     => $cost->description
                ),                                       
                "cost-{$cost->id}-amount" => array(
                    'type'      => 'textbox',
//                    'required'  => true,
                    'title'     => Text::get('costs-field-amount'),
                    'size'      => 8,
                    'class'     => 'inline cost-amount',
                    'hint'      => Text::get('tooltip-project-cost-amount'),
                    'value'     => $cost->amount
                ),
                "cost-{$cost->id}-required"  => array(
//                    'required'  => true,
                    'title'     => Text::get('costs-field-required_cost'),
                    'class'     => 'inline cost-required',
                    'type'      => 'radios',
                    'options'   => array (
                                        array(
                                                'value'     => '1',
                                                'label'     => Text::get('costs-field-required_cost-yes')
                                            ),
                                        array(
                                                'value'     => '0',
                                                'label'     => Text::get('costs-field-required_cost-no')
                                            )
                                    ),
                    'value'     => $cost->required,
                    'hint'      => Text::get('tooltip-project-cost-required'),
                ),
                "cost-{$cost->id}-dates" => array(
                    'type'      => 'group',
                    'title'     => Text::get('costs-field-dates'),
                    'class'     => 'inline cost-dates',
                    'hint'      => Text::get('tooltip-project-cost-dates'),
                    'children'  => array(
                        "cost-{$cost->id}-from"  => array(
                            'class'     => 'inline cost-from',
                            'type'      => 'datebox',
                            'size'      => 8,
                            'title'     => Text::get('costs-field-date_from'),
                            'value'     => $cost->from
                        ),
                        "cost-{$cost->id}-until"  => array(
                            'class'     => 'inline cost-until',
                            'title'     => Text::get('costs-field-date_until'),
                            'type'      => 'datebox',
                            'size'      => 8,
                            'value'     => $cost->until
                        )
                    )
                ),        
                "cost-{$cost->id}-remove" => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-remove-button'),
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
    'title'         => Text::get('costs-main-header'),
    'hint'          => Text::get('guide-project-costs'),    
    'class'         => 'aqua',
    'footer'        => array(
        'view-step-rewards' => array(
            'name'  => 'view-step-rewards',
            'type'  => 'submit',
            'label' => Text::get('form-next-button'),
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
            'title'     => Text::get('costs-fields-main-title'),
            'hint'      => Text::get('tooltip-project-costs'),
            'children'  => $costs  + array(
                'cost-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add',
                )                
            )
        ),
        
        'cost-meter' => array(
            'title'     => Text::get('costs-fields-metter-title'),
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
            'title'     => Text::get('costs-field-resoure'),
            'hint'      => Text::get('tooltip-project-resource'),
            'value'     => $project->resource
        )/*
        
        'schedule' => array(                        
            'title'     => Text::get('costs-field-schedule'),
            'class'     => 'fullwidth'
        ), */         
    )

));