<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;


$project = $vars['project'];
$errors = $project->errors[$vars['step']] ?: array();
$okeys  = $project->okeys[$vars['step']] ?: array();

$social_rewards = array();
$individual_rewards = array();

$txt_details = Text::get('regular-see_details');

foreach ($project->individual_rewards as $individual_reward) {

    // a ver si es el que estamos editando o no
    if (!empty($vars["individual_reward-{$individual_reward->id}-edit"])) {

        // lo mismo que para las licencias solamente para el texto en el tipo otro
        $types = array();

        foreach ($vars['itypes'] as $type) {

            if ($type->id == 'other') {
                // un campo para especificar el tipo
                $children = array(
                        "individual_reward-{$individual_reward->id}-other" => array(
                            'type'      => 'textbox',
                            'class'     => 'inline other',
                            'title'     => Text::get('rewards-field-individual_reward-other'),
                            'value'     => $individual_reward->other,
                            'name'      => "individual_reward-{$individual_reward->id}-{$type->id}",
                            'hint'     => Text::get('tooltip-project-individual_reward-icon-other')
                        )
                    );
            } else {
                // como tener children sin tenerlos
                $children = array(
                    "individual_reward-{$individual_reward->id}-{$type->id}" => array(
                        'type'      => 'hidden',
                        'name'      => "individual_reward-{$individual_reward->id}-other"
                    )
                );
            }

            $types["individual_reward-{$individual_reward->id}-icon-{$type->id}"] =  array(
                'name'  => "individual_reward-{$individual_reward->id}-icon",
                'value' => $type->id,
                'type'  => 'radio',
                'class' => "reward-type reward_{$type->id} individual_{$type->id}",
                'label' => $type->name,
                'hint'  => $type->description,
                'id'    => "individual_reward-{$individual_reward->id}-icon-{$type->id}",
                'checked' => $type->id == $individual_reward->icon ? true : false,
                'children' => $children
            );
        }

        // a este grupo le ponemos estilo de edicion
        $individual_rewards["individual_reward-{$individual_reward->id}"] = array(
                'type'      => 'group',
                'class'     => 'reward individual_reward editindividual_reward',
                'children'  => array(
                    "individual_reward-{$individual_reward->id}-edit" => array(
                        'type'      => 'hidden',
                        'value'     => '1'
                    ),
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
                    "individual_reward-{$individual_reward->id}-icon" => array(
                        'title'     => Text::get('rewards-field-individual_reward-type'),
                        'required'  => true,
                        'class'     => 'inline',
                        'type'      => 'group',
                        'children'  => $types,
                        'value'     => $individual_reward->icon,
                        'errors'    => !empty($errors["individual_reward-{$individual_reward->id}-icon"]) ? array($errors["individual_reward-{$individual_reward->id}-icon"]) : array(),
                        'ok'        => !empty($okeys["individual_reward-{$individual_reward->id}-icon"]) ? array($okeys["individual_reward-{$individual_reward->id}-icon"]) : array(),
                        'hint'      => Text::get('tooltip-project-individual_reward-type')
                    ),
                    "individual_reward-{$individual_reward->id}-amount" => array(
                        'title'     => Text::get('rewards-field-individual_reward-amount'),
                        'required'  => true,
                        'type'      => 'textbox',
                        'size'      => 5,
                        'class'     => 'inline reward-amount',
                        'value'     => $individual_reward->amount_original,
                        'errors'    => !empty($errors["individual_reward-{$individual_reward->id}-amount"]) ? array($errors["individual_reward-{$individual_reward->id}-amount"]) : array(),
                        'ok'        => !empty($okeys["individual_reward-{$individual_reward->id}-amount"]) ? array($okeys["individual_reward-{$individual_reward->id}-amount"]) : array(),
                        'hint'      => Text::get('tooltip-project-individual_reward-amount'),
                        'symbol'     => $individual_reward->currency_html
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
                                'class' => 'inline remove weak'
                            )
                        )
                    )
                )
            );

    } else {

        $individual_rewards["individual_reward-{$individual_reward->id}"] = array(
            'class'     => 'reward individual_reward',
            'view'      => 'project/edit/rewards/reward.html.php',
            'data'      => array('reward' => $individual_reward, 'types' => $vars['itypes']),
        );

    }
}

$sfid = 'sf-project-rewards';


echo SuperForm::get(array(

    'id'            => $sfid,
    'action'        => '',
    'level'         => $vars['level'],
    'method'        => 'post',
    'title'         => Text::get('rewards-main-header'),
    'hint'          => Text::get('guide-project-rewards'),
    'class'         => 'aqua',
    'elements'      => array(
        'process_rewards' => array (
            'type' => 'hidden',
            'value' => 'rewards'
        ),

        'anchor-rewards' => array(
            'type' => 'html',
            'html' => '<a name="rewards"></a>'
        ),

        'individual_rewards' => array(
            'type'      => 'group',
            'required'  => true,
            'title'     => Text::get('rewards-fields-individual_reward-title'),
            'hint'      => Text::get('tooltip-project-individual_rewards'),
            'class'     => 'rewards',
            'errors'    => !empty($errors["individual_rewards"]) ? array($errors["individual_rewards"]) : array(),
            'ok'        => !empty($okeys["individual_rewards"]) ? array($okeys["individual_rewards"]) : array(),
            'children'  => $individual_rewards + array(
                'individual_reward-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add reward-add red',
                )
            )
        ),

        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'errors' => array(
                    'title' => Text::get('form-footer-errors_title'),
                    'view'  => new View('project/edit/errors.html.php', array(
                        'project'   => $project,
                        'step'      => $vars['step']
                    ))
                ),
                'buttons'  => array(
                    'type'  => 'group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'submit',
                            'name'  => 'view-step-'.$vars['next'],
                            'label' => Text::get('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )
        )

    )

));
?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function () {

    /* social rewards buttons */
    var socials = $('div#<?php echo $sfid ?> li.element#li-social_rewards');

    //abrir el selector de licencias
    socials.delegate('li.element.social_reward input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        socials.superform({data:data});
    });

    //boton ok, cierra el selector
    socials.delegate('li.element.editsocial_reward input.ok', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        socials.superform({data:data});
    });

    //borra la licencia
    socials.delegate('li.element.editsocial_reward input.remove, li.element.social_reward input.remove', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        socials.superform({data:data});
    });
    //añadir nueva licencia
    socials.delegate('#li-social_reward-add input', 'click', function (event) {
       event.preventDefault();
       var data = {};
       data[this.name] = '1';
       socials.superform({data:data});
    });
    //despliege de radio buttons en las licencias
    socials.delegate('li.element input[type="radio"]', 'change', function(event){
        var input = $(event.target);
        var li = input.closest('li.group');
        $(this).closest('li.group').first().find('input[type="radio"][name="' + input.attr('name') + '"]').each(function(i, r){
            try {
              if (input.attr('id') == r.id) {
                  $('div.children#' + r.id + '-children').slideDown(400);
              } else {
                  $('div.children#' + r.id + '-children').slideUp(400);
              }
            } catch (e) {}
        })
    });

    /* individual_rewards buttons */
    var individuals = $('div#<?php echo $sfid ?> li.element#li-individual_rewards');

    individuals.delegate('li.element.individual_reward input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        individuals.superform({data:data});
    });

    individuals.delegate('li.element.editindividual_reward input.ok', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        individuals.superform({data:data});
    });

    individuals.delegate('li.element.editindividual_reward input.remove, li.element.individual_reward input.remove', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        individuals.superform({data:data});
    });

    individuals.delegate('#li-individual_reward-add input', 'click', function (event) {
       event.preventDefault();
       var data = {};
       data[this.name] = '1';
       individuals.superform({data:data});
    });

});
// @license-end
</script>
