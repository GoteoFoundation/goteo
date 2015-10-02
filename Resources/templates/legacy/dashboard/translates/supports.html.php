<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $vars['project'];
$errors = $vars['errors'];

$supports = array();

if (!empty($project->supports)) {

    foreach ($project->supports as $support) {

        $ch = array();

        // a ver si es el que estamos editando o no
        if (!empty($vars["support-{$support->id}-edit"])) {

            $original = \Goteo\Model\Project\Support::get($support->id);

            // a este grupo le ponemos estilo de edicion
        $supports["support-{$support->id}"] = array(
                'type'      => 'group',
                'class'     => 'support editsupport',
                'children'  => array(
                    "support-{$support->id}-edit" => array(
                        'type'      => 'hidden',
                        'value'      => '1'
                    ),
                    "support-{$support->id}-support-orig" => array(
                        'title'     => Text::get('supports-field-support'),
                        'type'      => 'html',
                        'html'      => $original->support
                    ),
                    "support-{$support->id}-support" => array(
                        'title'     => '',
                        'type'      => 'textbox',
                        'size'      => 100,
                        'class'     => 'inline',
                        'value'     => $support->support,
                        'errors'    => !empty($errors["support-{$support->id}-support"]) ? array($errors["support-{$support->id}-support"]) : array(),
                        'ok'        => !empty($okeys["support-{$support->id}-support"]) ? array($okeys["support-{$support->id}-support"]) : array(),
                        'hint'      => Text::get('tooltip-project-support-support')
                    ),
                    "support-{$support->id}-description-orig" => array(
                        'title'     => Text::get('supports-field-description'),
                        'type'      => 'html',
                        'html'      => nl2br($original->description)
                    ),
                    "support-{$support->id}-description" => array(
                        'type'      => 'textarea',
                        'title'     => '',
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
                            )
                        )
                    )
                )
            );

        } else {

            $supports["support-{$support->id}"] = array(
                'class'     => 'support',
                'view'      => 'dashboard/translates/supports/support.html.php',
                'data'      => array('support' => $support),
            );
        }


    }
}


$sfid = 'sf-project-supports';

?>

<form method="post" action="/dashboard/translates/supports/save" class="project" enctype="multipart/form-data">

<?php echo new SuperForm(array(
    'id'            => $sfid,
    'action'        => '',
    'level'         => 3,
    'method'        => 'post',
    'title'         => '',
    'hint'          => Text::get('guide-project-supports'),
    'class'         => 'aqua',
    /*
     'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save-supports',
            'label' => Text::get('regular-save'),
            'class' => 'next'
        )
    ),
    */
    'elements'      => array(
        'process_supports' => array (
            'type' => 'hidden',
            'value' => 'supports'
        ),
        'supports' => array(
            'type'      => 'group',
            'title'     => Text::get('supports-fields-support-title'),
            'hint'      => Text::get('tooltip-project-supports'),
            'children'  => $supports
        )
    )

));
?>
</form>
<script type="text/javascript">
$(function () {

    var supports = $('div#<?php echo $sfid ?> li.element#li-supports');

    supports.delegate('li.element.support input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        supports.superform({data:data});
    });

    supports.delegate('li.element.editsupport input.ok', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        supports.superform({data:data});
    });

});
</script>
