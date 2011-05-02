<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            

$project = $this['project'];

$social_rewards_types = array();
$social_rewards_licenses = array();
$social_rewards = array();

foreach ($this['stypes'] as $id => $type) {
    $social_rewards_types[] = array(
        'value' => $id,
        'class' => "social_{$id}",
        'label' => $type
    );
}


foreach ($project->social_rewards as $social_reward) {
    
    $social_rewards["social_reward-{$social_reward->id}"] = array(
            'type'      => 'group',      
            'class'     => 'reward social_reward',
            'children'  => array(                         
                "social_reward-{$social_reward->id}-reward" => array(
                    'title'     => 'Resumen',
                    'type'      => 'textbox',
                    'size'      => 100,
                    'class'     => 'inline',
                    'value'     => $social_reward->reward,
                    'hint'      => Text::get('tooltip-project-social_reward-reward'),
                ),
                "social_reward-{$social_reward->id}-icon" => array(
                    'title'     => 'Tipo',
                    'class'     => 'inline social_reward-type reward-type',
                    'type'      => 'radios',
                    'options'   => $social_rewards_types,
                    'value'     => $social_reward->icon,
                    'hint'      => Text::get('tooltip-project-social_reward-icon'),
                ),
                "social_reward-{$social_reward->id}-description" => array(
                    'type'      => 'textarea',
                    'title'     => 'Descripción',
                    'cols'      => 100,
                    'rows'      => 4,
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-project-social_reward-description'),
                    'value'     => $social_reward->description
                ),                                                       
                "social_reward-{$social_reward->id}-remove" => array(
                    'type'  => 'submit',
                    'label' => 'Quitar',
                    'class' => 'inline remove reward-remove'
                )
            )
        );
}

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
            'class'     => 'rewards social_rewards',
            'children'  => $social_rewards + array(
                'social_reward-add' => array(
                    'type'  => 'submit',
                    'label' => 'Añadir',
                    'class' => 'add',
                )
            )
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