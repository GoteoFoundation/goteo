<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            

$project = $this['project'];
$errors = $project->errors[$this['step']] ?: array();
$okeys  = $project->okeys[$this['step']] ?: array();

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

    // a ver si es el que estamos editando o no
    if (isset($_POST["social_reward-{$social_reward->id}-edit"])) {
        // a este grupo le ponemos estilo de edicion
        $social_rewards["social_reward-{$social_reward->id}"] = array(
                'type'      => 'group',
                'class'     => 'reward social_reward edit',
                'children'  => array(
                    "social_reward-{$social_reward->id}-reward" => array(
                        'title'     => Text::get('rewards-field-social_reward-reward'),
                        'type'      => 'textbox',
                        'required'  => true,
                        'size'      => 100,
                        'class'     => 'inline',
                        'value'     => $social_reward->reward,
                        'errors'    => !empty($errors["social_reward-{$social_reward->id}-reward"]) ? array($errors["social_reward-{$social_reward->id}-reward"]) : array(),
                        'ok'        => !empty($okeys["social_reward-{$social_reward->id}-reward"]) ? array($okeys["social_reward-{$social_reward->id}-reward"]) : array(),
                        'hint'      => Text::get('tooltip-project-social_reward-reward')
                    ),
                    "social_reward-{$social_reward->id}-icon" => array(
                        'title'     => Text::get('rewards-field-social_reward-type'),
                        'class'     => 'inline social_reward-type reward-type',
                        'type'      => 'radios',
                        'required'  => true,
                        'options'   => $social_rewards_types,
                        'value'     => $social_reward->icon,
                        'errors'    => !empty($errors["social_reward-{$social_reward->id}-icon"]) ? array($errors["social_reward-{$social_reward->id}-icon"]) : array(),
                        'ok'        => !empty($okeys["social_reward-{$social_reward->id}-icon"]) ? array($okeys["social_reward-{$social_reward->id}-icon"]) : array(),
                        'hint'      => Text::get('tooltip-project-social_reward-type')
                    ),
                    "social_reward-{$social_reward->id}-description" => array(
                        'type'      => 'textarea',
                        'required'  => true,
                        'title'     => Text::get('rewards-field-social_reward-description'),
                        'cols'      => 100,
                        'rows'      => 4,
                        'class'     => 'inline',
                        'value'     => $social_reward->description,
                        'errors'    => !empty($errors["social_reward-{$social_reward->id}-description"]) ? array($errors["social_reward-{$social_reward->id}-description"]) : array(),
                        'ok'        => !empty($okeys["social_reward-{$social_reward->id}-description"]) ? array($okeys["social_reward-{$social_reward->id}-description"]) : array(),
                        'hint'      => Text::get('tooltip-project-social_reward-description')
                    ),
                    "social_reward-{$social_reward->id}-license" => array(
                        'type'      => 'radios',
                        'title'     => Text::get('rewards-field-social_reward-license'),
                        'options'   => $social_rewards_licenses,
                        'value'     => $social_reward->license,
                        'class'     => 'inline reward-license',
                        'errors'    => !empty($errors["social_reward-{$social_reward->id}-license"]) ? array($errors["social_reward-{$social_reward->id}-license"]) : array(),
                        'ok'        => !empty($okeys["social_reward-{$social_reward->id}-license"]) ? array($okeys["social_reward-{$social_reward->id}-license"]) : array(),
                        'hint'      => Text::get('tooltip-project-social_reward-license')
                    ),
                    "social_reward-{$social_reward->id}-remove" => array(
                        'type'  => 'submit',
                        'label' => Text::get('form-remove-button'),
                        'class' => 'inline remove reward-remove'
                    ),
                    "social_reward-{$social_reward->id}-accept" => array(
                        'type'  => 'submit',
                        'label' => Text::get('form-accept-button'),
                        'class' => 'inline accept reward-accept'
                    )
                )
            );
    } else {
        // a este grupo lo ponemos cerrado, en html y boton para ir a editarlo
        // ese boton lanza el formulario igual que lo hace el de añadir, quitar o aceptar
        // a ver si el tipo de registro lo podemos poner con el icono
        // y el boton de editar en la misma linea
        $social_rewards["social_reward-{$social_reward->id}"] = array(
                'type'      => 'group',
                'class'     => 'reward social_reward line',
                'children'  => array(
                    "social_reward-{$social_reward->id}-reward" => array(
                        'type'      => 'html',
                        'class'     => 'inline',
                        'html'      => $this['stypes'][$social_reward->icon]->name . ': ' . $social_reward->reward . ' ' . $social_reward->icon
                    ),
                    "social_reward-{$social_reward->id}-edit" => array(
                        'type'  => 'submit',
                        'label' => Text::get('form-edit-button'),
                        'class' => 'inline edit reward-edit'
                    )
                )
            );
    }

}

foreach ($project->individual_rewards as $individual_reward) {

    // a ver si es el que estamos editando o no
    if (isset($_POST["individual_reward-{$individual_reward->id}-edit"])) {
        // a este grupo le ponemos estilo de edicion
        $individual_rewards["individual_reward-{$individual_reward->id}"] = array(
                'type'      => 'group',
                'class'     => 'reward individual_reward',
                'children'  => array(
                    "individual_reward-{$individual_reward->id}-reward" => array(
                        'title'     => Text::get('rewards-field-individual_reward-reward'),
                        'required'  => true,
                        'type'      => 'textbox',
                        'size'      => 100,
                        'class'     => 'inline',
                        'value'     => $individual_reward->reward,
                        'errors'    => !empty($errors["individual_reward-{$individual_reward->id}-reward"]) ? array($errors["individual_reward-{$individual_reward->id}-reward"]) : array(),
                        'ok'        => !empty($okeys["individual_reward-{$individual_reward->id}-reward"]) ? array($okeys["individual_reward-{$individual_reward->id}-reward"]) : array(),
                        'hint'      => Text::get('tooltip-project-individual_reward-reward')
                    ),
                    "individual_reward-{$individual_reward->id}-icon" => array(
                        'title'     => Text::get('rewards-field-individual_reward-type'),
                        'required'  => true,
                        'class'     => 'inline  reward-type',
                        'type'      => 'radios',
                        'options'   => $individual_rewards_types,
                        'value'     => $individual_reward->icon,
                        'errors'    => !empty($errors["individual_reward-{$individual_reward->id}-icon"]) ? array($errors["individual_reward-{$individual_reward->id}-icon"]) : array(),
                        'ok'        => !empty($okeys["individual_reward-{$individual_reward->id}-icon"]) ? array($okeys["individual_reward-{$individual_reward->id}-icon"]) : array(),
                        'hint'      => Text::get('tooltip-project-individual_reward-type')
                    ),
                    "individual_reward-{$individual_reward->id}-description" => array(
                        'type'      => 'textarea',
                        'required'  => true,
                        'title'     => Text::get('rewards-field-individual_reward-description'),
                        'cols'      => 100,
                        'rows'      => 4,
                        'class'     => 'inline',
                        'value'     => $individual_reward->description,
                        'errors'    => !empty($errors["individual_reward-{$individual_reward->id}-description"]) ? array($errors["individual_reward-{$individual_reward->id}-description"]) : array(),
                        'ok'        => !empty($okeys["individual_reward-{$individual_reward->id}-description"]) ? array($okeys["individual_reward-{$individual_reward->id}-description"]) : array(),
                        'hint'      => Text::get('tooltip-project-individual_reward-description')
                    ),
                    "individual_reward-{$individual_reward->id}-amount" => array(
                        'title'     => Text::get('rewards-field-individual_reward-amount'),
                        'required'  => true,
                        'type'      => 'textbox',
                        'size'      => 5,
                        'class'     => 'inline reward-amount',
                        'value'     => $individual_reward->amount,
                        'errors'    => !empty($errors["individual_reward-{$individual_reward->id}-amount"]) ? array($errors["individual_reward-{$individual_reward->id}-amount"]) : array(),
                        'ok'        => !empty($okeys["individual_reward-{$individual_reward->id}-amount"]) ? array($okeys["individual_reward-{$individual_reward->id}-amount"]) : array(),
                        'hint'      => Text::get('tooltip-project-individual_reward-amount')
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
                    ),
                    "individual_reward-{$individual_reward->id}-accept" => array(
                        'type'  => 'submit',
                        'label' => Text::get('form-accept-button'),
                        'class' => 'inline remove reward-accept'
                    )
                )
            );
                    
    } else {
        // a este grupo lo ponemos cerrado, en html y boton para ir a editarlo
        // ese boton lanza el formulario igual que lo hace el de añadir, quitar o aceptar
        // a ver si el tipo de registro lo podemos poner con el icono
        // y el boton de editar en la misma linea
        $individual_rewards["individual_reward-{$individual_reward->id}"] = array(
                'type'      => 'group',
                'class'     => 'reward individual_reward line',
                'children'  => array(
                    "individual_reward-{$individual_reward->id}-reward" => array(
                        'type'      => 'html',
                        'class'     => 'inline',
                        'html'      => $this['itypes'][$individual_reward->icon]->name . ': ' . $individual_reward->reward
                    ),
                    "individual_reward-{$individual_reward->id}-edit" => array(
                        'type'  => 'submit',
                        'label' => Text::get('form-edit-button'),
                        'class' => 'inline edit reward-edit'
                    )
                )
            );
    }
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