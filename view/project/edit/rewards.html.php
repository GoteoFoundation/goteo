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
    
    $licenses[] = array(
        'label' => 'Ninguna',
        'value' => '',
        'class' => ''
    );
    
    foreach ($type->licenses as $lid => $license) {        
        
        $licenses[] = array(
            'label' => $license->name,
            'value' => $license->id,            
            'class' => 'license_' . $license->id
        );

    }            

    $social_rewards_types[] = array(
        'value' => $id,
        'class' => "reward_{$id} social_{$id}",
        'label' => $type->name,
        'children' => array(
            array(
                'type'      => 'radios',
                'name'      => 'license[]',
                'class'     => 'license',
                'title'     => Text::get('rewards-field-social_reward-license'),
                'options'   => $licenses
            )
        )
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

//print_r($this);die;

foreach ($project->social_rewards as $social_reward) {

    // a ver si es el que estamos editando o no
    if ($social_reward->id === $this['editsocial_reward']) {
        
        
        // a este grupo le ponemos estilo de edicion
        $social_rewards["social_reward-{$social_reward->id}"] = array(
                'type'      => 'group',
                'class'     => 'reward social_reward editsocial_reward',
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
                    "social_reward-{$social_reward->id}-description" => array(
                        'type'      => 'textarea',
                        'required'  => true,
                        'title'     => Text::get('rewards-field-social_reward-description'),
                        'cols'      => 100,
                        'rows'      => 4,
                        'class'     => 'inline reward-description',
                        'value'     => $social_reward->description,
                        'errors'    => !empty($errors["social_reward-{$social_reward->id}-description"]) ? array($errors["social_reward-{$social_reward->id}-description"]) : array(),
                        'ok'        => !empty($okeys["social_reward-{$social_reward->id}-description"]) ? array($okeys["social_reward-{$social_reward->id}-description"]) : array(),
                        'hint'      => Text::get('tooltip-project-social_reward-description')
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
                                /*
                    "social_reward-{$social_reward->id}-license" => array(
                        'type'      => 'radios',
                        'title'     => Text::get('rewards-field-social_reward-license'),
                        'options'   => $social_rewards_licenses,
                        'value'     => $social_reward->license,
                        'class'     => 'inline reward-license',
                        'errors'    => !empty($errors["social_reward-{$social_reward->id}-license"]) ? array($errors["social_reward-{$social_reward->id}-license"]) : array(),
                        'ok'        => !empty($okeys["social_reward-{$social_reward->id}-license"]) ? array($okeys["social_reward-{$social_reward->id}-license"]) : array(),
                        'hint'      => Text::get('tooltip-project-social_reward-license')
                    ),*/
                    "social_reward-{$social_reward->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'buttons',
                        'children' => array(
                            "social_reward-{$social_reward->id}-ok" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline ok'
                            ),
                            "social_reward-{$social_reward->id}-remove" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-remove-button'),
                                'class' => 'inline remove'
                            )
                        )
                    )
                )
            );
    } else {

        $social_rewards["social_reward-{$social_reward->id}"] = array(
            'class'     => 'reward social_reward',
            'view'      => 'view/project/edit/rewards/reward.html.php',
            'data'      => array('reward' => $social_reward, 'license' => $this['licenses'][$social_reward->license]),
        );
        
    }

}

foreach ($project->individual_rewards as $individual_reward) {

    // a ver si es el que estamos editando o no
    if ($individual_reward->id === $this['editindividual_reward']) {
        // a este grupo le ponemos estilo de edicion
        $individual_rewards["individual_reward-{$individual_reward->id}"] = array(
                'type'      => 'group',
                'class'     => 'reward individual_reward editindividual_reward',
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
                        'class'     => 'inline reward-description',
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
                    "individual_reward-{$individual_reward->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'buttons',
                        'children' => array(
                            "individual_reward-{$individual_reward->id}-ok" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline ok'
                            ),
                            "individual_reward-{$individual_reward->id}-remove" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-remove-button'),
                                'class' => 'inline remove'
                            )
                        )
                    )
                )
            );
                    
    } else {

        $individual_rewards["individual_reward-{$individual_reward->id}"] = array(
            'class'     => 'reward individual_reward',
            'view'      => 'view/project/edit/rewards/reward.html.php',
            'data'      => array('reward' => $individual_reward),
        );
        
    }
}

$sfid = 'sf-project-rewards';

echo new SuperForm(array(

    'id'            => $sfid,
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
                    'label' => Text::get('form-add-button'),
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
?>
<script type="text/javascript">
$(function () {

    /* social rewards buttons */
    var socials = $('div#<?php echo $sfid ?> li.element#social_rewards');

    socials.delegate('li.element.social_reward input.edit', 'click', function (event) {
        var data = {};
        data[this.name] = '1';
        Superform.update(socials, data);
        event.preventDefault();
    });

    socials.delegate('li.element.social_reward input.ok', 'click', function (event) {
        var data = {};
        data[this.name.substring(0, 18) + 'edit'] = '0';
        Superform.update(socials, data);
        event.preventDefault();
    });

    socials.delegate('li.element.editsocial_reward input.remove, li.element.social_reward input.remove', 'click', function (event) {
        var data = {};
        data[this.name] = '1';
        Superform.update(socials, data);
        event.preventDefault();
    });

    socials.delegate('#social_reward-add input', 'click', function (event) {
       var data = {};
       data[this.name] = '1';
       Superform.update(socials, data);
       event.preventDefault();
    });

    /* individual_rewards buttons */
    var individuals = $('div#<?php echo $sfid ?> li.element#individual_rewards');

    individuals.delegate('li.element.individual_reward input.edit', 'click', function (event) {
        var data = {};
        data[this.name] = '1';
        Superform.update(individuals, data);
        event.preventDefault();
    });

    individuals.delegate('li.element.editindividual_reward input.ok', 'click', function (event) {
        var data = {};
        data[this.name.substring(0, 22) + 'edit'] = '0';
        Superform.update(individuals, data);
        event.preventDefault();
    });

    individuals.delegate('li.element.editindividual_reward input.remove, li.element.individual_reward input.remove', 'click', function (event) {
        var data = {};
        data[this.name] = '1';
        Superform.update(individuals, data);
        event.preventDefault();
    });

    individuals.delegate('#individual_reward-add input', 'click', function (event) {
       var data = {};
       data[this.name] = '1';
       Superform.update(individuals, data);
       event.preventDefault();
    });

});
</script>