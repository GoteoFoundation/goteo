<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            

$project = $this['project'];

$social_rewards_types = array();
$social_rewards_licenses = array();
$social_rewards = array();

$individual_rewards = array();
$individual_rewards_types = array();

foreach ($this['stypes'] as $id => $type) {

    $licenses = array();
    foreach ($type->licenses as $lid => $license) {
        $licenses[$license->id] = array(
            'value' => $license->id,
            'class' => "license_{$license->id}",
            'label' => $license
        );
    }


    $social_rewards_types[] = array(
        'value' => $id,
        'class' => "reward_{$id} social_{$id}",
        'label' => $type->name,
        'children' => $licenses
    );
}

foreach ($this['itypes'] as $id => $type) {
    $individual_rewards_types[] = array(
        'value' => $id,
        'class' => "reward_{$id} individual_{$id}",
        'label' => $type->name
    );
}

foreach ($this['licenses'] as $id => $license) {
    $social_rewards_licenses[] = array(
        'value' => $id,
        'class' => "license_{$id}",
        'label' => $license
    );
}

foreach ($project->social_rewards as $social_reward) {
    
    $social_rewards["social_reward-{$social_reward->id}"] = array(
            'type'      => 'group',      
            'class'     => 'reward social_reward',
            'children'  => array(                         
                "social_reward-{$social_reward->id}-reward" => array(
                    'title'     => Text::get('rewards-field-social_reward-reward'),
                    'type'      => 'textbox',
//                    'required'  => true,
                    'size'      => 100,
                    'class'     => 'inline',
                    'value'     => $social_reward->reward,
                    'hint'      => Text::get('tooltip-project-social_reward-reward'),
                ),
                "social_reward-{$social_reward->id}-icon" => array(
                    'title'     => Text::get('rewards-field-social_reward-type'),
                    'class'     => 'inline social_reward-type reward-type',
                    'type'      => 'radios',
//                    'required'  => true,
                    'options'   => $social_rewards_types,
                    'value'     => $social_reward->icon,
                    'hint'      => Text::get('tooltip-project-social_reward-icon'),
                ),
                "social_reward-{$social_reward->id}-description" => array(
                    'type'      => 'textarea',
//                    'required'  => true,
                    'title'     => Text::get('rewards-field-social_reward-description'),
                    'cols'      => 100,
                    'rows'      => 4,
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-project-social_reward-description'),
                    'value'     => $social_reward->description
                ),    
                "social_reward-{$social_reward->id}-license" => array(
                    'type'      => 'radios',
                    'title'     => Text::get('rewards-field-social_reward-license'),
                    'options'   => $social_rewards_licenses,                    
                    'value'     => $social_reward->license,
                    'class'     => 'inline reward-license',
                    'hint'      => Text::get('tooltip-project-social_reward-license')                    
                ), 
                "social_reward-{$social_reward->id}-remove" => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-remove-button'),
                    'class' => 'inline remove reward-remove'
                )
            )
        );
}

foreach ($project->individual_rewards as $individual_reward) {
    
    $individual_rewards["individual_reward-{$individual_reward->id}"] = array(
            'type'      => 'group',      
            'class'     => 'reward individual_reward',
            'children'  => array(                         
                "individual_reward-{$individual_reward->id}-reward" => array(
                    'title'     => Text::get('rewards-field-individual_reward-reward'),
                    'type'      => 'textbox',
                    'size'      => 100,
                    'class'     => 'inline',
                    'value'     => $individual_reward->reward,
                    'hint'      => Text::get('tooltip-project-individual_reward-reward'),
                ),
                "individual_reward-{$individual_reward->id}-icon" => array(
                    'title'     => Text::get('rewards-field-individual_reward-type'),
                    'class'     => 'inline  reward-type',
                    'type'      => 'radios',
                    'options'   => $individual_rewards_types,
                    'value'     => $individual_reward->icon,
                    'hint'      => Text::get('tooltip-individual_reward-social_reward-icon'),
                ),
                "individual_reward-{$individual_reward->id}-description" => array(
                    'type'      => 'textarea',
                    'title'     => Text::get('rewards-field-individual_reward-description'),
                    'cols'      => 100,
                    'rows'      => 4,
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-project-individual_reward-description'),
                    'value'     => $individual_reward->description
                ),                                    
                "individual_reward-{$individual_reward->id}-amount" => array(
                    'title'     => Text::get('rewards-field-individual_reward-amount'),
                    'type'      => 'textbox',
                    'size'      => 5,
                    'class'     => 'inline reward-amount',
                    'value'     => $individual_reward->amount,
                    'hint'      => Text::get('tooltip-project-individual_reward-amount'),
                ),
                "individual_reward-{$individual_reward->id}-units" => array(
                    'title'     => Text::get('rewards-field-individual_reward-units'),
                    'type'      => 'textbox',
                    'size'      => 5,
                    'class'     => 'inline reward-units',
                    'value'     => $individual_reward->units,
                    'hint'      => Text::get('tooltip-project-individual_reward-units'),
                ),
                "individual_reward-{$individual_reward->id}-remove" => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-remove-button'),
                    'class' => 'inline remove reward-remove'
                )
            )
        );
}

echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('rewards-main-header'),
    'hint'          => Text::get('guide-project-rewards'),    
    'class'         => 'aqua',
    'footer'        => array(
        'view-step-supports' => array(
            'type'  => 'submit',
            'name'  => 'view-step-supports',
            'label' => Text::get('form-next-button'),
            'class' => 'next'
        )        
    ),    
    'elements'      => array(
        'process_rewards' => array (
            'type' => 'hidden',
            'value' => 'rewards'
        ),
        
        'social_rewards' => array(
            'type'      => 'group',
            'title'     => Text::get('rewards-fields-social_reward-title'),
            'hint'      => Text::get('tooltip-project-social_rewards'),
            'class'     => 'rewards',
            'children'  => $social_rewards + array(
                'social_reward-add' => array(
                    'type'  => 'submit',
                    'label' => 'Añadir',
                    'class' => 'add reward-add',
                )
            )
        ),
        
        'individual_rewards' => array(
            'type'      => 'group',
            'title'     => Text::get('rewards-fields-individual_reward-title'),
            'hint'      => Text::get('tooltip-project-individual_rewards'),
            'class'     => 'rewards',
            'children'  => $individual_rewards + array(
                'individual_reward-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add reward-add',
                )
            )
        )          
    )

));