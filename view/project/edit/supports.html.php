<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;
            
$project = $this['project'];
$errors = $project->errors[$this['step']] ?: array();
$okeys  = $project->okeys[$this['step']] ?: array();

$supports = array();

foreach ($project->supports as $support) {

    $ch = array();

    // a ver si es el que estamos editando o no
    if ($support->id === $this['editsupport']) {


        $support_types = array();

        foreach ($this['types'] as $id => $type) {
            $support_types["support-{$support->id}-type-{$id}"] = array(
                'name'  => "support-{$support->id}-type",
                'value' => $id,
                'type'  => 'radio',
                'class' => "support-type support_{$id}",
                'hint'  => Text::get('tooltip-project-support-type-'.$id),
                'label' => $type,
                'checked' => $id == $support->type  ? true : false
            );
        }


        // a este grupo le ponemos estilo de edicion
        $supports["support-{$support->id}"] = array(
                'type'      => 'group',
                'class'     => 'support editsupport',
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
                        'hint'      => Text::get('tooltip-project-support-support')
                    ),
                    "support-{$support->id}-type" => array(
                        'title'     => Text::get('supports-field-type'),
                        'required'  => true,
                        'class'     => 'inline',
                        'type'      => 'group',
                        'value'     => $support->type,
                        'children'  => $support_types,
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
                        'class'     => 'inline support-description',
                        'value'     => $support->description,
                        'errors'    => !empty($errors["support-{$support->id}-description"]) ? array($errors["support-{$support->id}-description"]) : array(),
                        'ok'        => !empty($okeys["support-{$support->id}-description"]) ? array($okeys["support-{$support->id}-description"]) : array(),
                        'hint'      => Text::get('tooltip-project-support-description')
                    ),
                    "support-{$support->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'buttons',
                        'children' => array(
                            "support-{$support->id}-ok" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline ok'
                            ),
                            "support-{$support->id}-remove" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-remove-button'),
                                'class' => 'inline remove weak'
                            )
                        )
                    )
                )
            );

    } else {

        $supports["support-{$support->id}"] = array(
            'class'     => 'support',
            'view'      => 'view/project/edit/supports/support.html.php',
            'data'      => array('support' => $support),
        );

    }


}

$sfid = 'sf-project-supports';

echo new SuperForm(array(

    'id'            => $sfid,

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
        'supports' => array(
            'type'      => 'group',
            'title'     => Text::get('supports-fields-support-title'),
            'hint'      => Text::get('tooltip-project-supports'),
            'children'  => $supports + array(
                'support-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add support-add red',
                )
            )
        )        
    )

));
?>
<script type="text/javascript">
$(function () {

    var supports = $('div#<?php echo $sfid ?> li.element#supports');

    supports.delegate('li.element.support input.edit', 'click', function (event) {
        var data = {};
        data[this.name] = '1';
        Superform.update(supports, data);
        event.preventDefault();
    });

    supports.delegate('li.element.editsupport input.ok', 'click', function (event) {
        var data = {};
        data[this.name.substring(0, 12) + 'edit'] = '0';
        Superform.update(supports, data);
        event.preventDefault();
    });

    supports.delegate('li.element.editsupport input.remove, li.element.support input.remove', 'click', function (event) {
        var data = {};
        data[this.name] = '1';
        Superform.update(supports, data);
        event.preventDefault();
    });

    supports.delegate('#support-add input', 'click', function (event) {
       var data = {};
       data[this.name] = '1';
       Superform.update(supports, data);
       event.preventDefault();
    });

});
</script>