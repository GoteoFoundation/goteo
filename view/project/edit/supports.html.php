<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            
$project = $this['project'];
$errors = $project->errors[$this['step']] ?: array();
$okeys  = $project->okeys[$this['step']] ?: array();

$support_types = array();

foreach ($this['types'] as $id => $type) {
    $support_types[] = array(
        'value' => $id,
        'class' => "support_{$id}",
        'label' => $type
    );
}

$supports = array();

foreach ($project->supports as $support) {
    
    $supports["support-{$support->id}"] = array(
            'type'      => 'group',      
            'class'     => 'support',
            'children'  => array(                         
                "support-{$support->id}-support" => array(
                    'title'     => Text::get('supports-field-support'),
                    'type'      => 'textbox',
                    'required'  => true,
                    'size'      => 100,
                    'class'     => 'inline',
                    'value'     => $support->support,
                    'errors'    => !empty($errors["support-{$support->id}-support"]) ? array($errors["support-{$support->id}-support"]) : array(),
                    'ok'        => !empty($okeys["support-{$support->id}-support"]) ? array($okeys["support-{$support->id}-support"]) : array(),
                    'hint'      => Text::get('tooltip-project-support-support'),
                ),
                "support-{$support->id}-type" => array(
                    'title'     => Text::get('supports-field-type'),
                    'required'  => true,
                    'class'     => 'inline support-type',
                    'type'      => 'radios',
                    'options'   => $support_types,
                    'value'     => $support->type,
                    'errors'    => !empty($errors["support-{$support->id}-type"]) ? array($errors["support-{$support->id}-type"]) : array(),
                    'ok'        => !empty($okeys["support-{$support->id}-type"]) ? array($okeys["support-{$support->id}-type"]) : array(),
                    'hint'      => Text::get('tooltip-project-support-type')
                ),
                "support-{$support->id}-description" => array(
                    'type'      => 'textarea',
                    'required'  => true,
                    'title'     => Text::get('supports-field-description'),
                    'cols'      => 100,
                    'rows'      => 4,
                    'class'     => 'inline',
                    'value'     => $support->description,
                    'errors'    => !empty($errors["support-{$support->id}-description"]) ? array($errors["support-{$support->id}-description"]) : array(),
                    'ok'        => !empty($okeys["support-{$support->id}-description"]) ? array($okeys["support-{$support->id}-description"]) : array(),
                    'hint'      => Text::get('tooltip-project-support-description')
                ),
                "support-{$support->id}-remove" => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-remove-button'),
                    'class' => 'inline remove support-remove'
                ),
                "support-{$support->id}-accept" => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-accept-button'),
                    'class' => 'inline accept support-accept'
                )
            )
        );
}

echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => Text::get('supports-main-header'),
    'hint'          => Text::get('guide-project-supports'),    
    'class'         => 'aqua',
    'footer'        => array(                        
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'view-step-preview',
            'label' => Text::get('form-next-button'),
            'class' => 'next'
        )        
    ),    
    'elements'      => array(        
        'process_supports' => array (
            'type' => 'hidden',
            'value' => 'supports'
        ),
        'suppports' => array(
            'type'      => 'group',
            'title'     => Text::get('supports-fields-support-title'),
            'hint'      => Text::get('tooltip-project-supports'),
            'children'  => $supports + array(
                'support-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add support-add',
                )
            )
        )        
    )

));