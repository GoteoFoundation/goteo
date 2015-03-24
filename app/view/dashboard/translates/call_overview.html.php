<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$call = $this['call'];
$original = $this['original'];
$errors = $this['errors'];

$categories = array();

foreach ($this['categories'] as $value => $label) {
    $categories[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => in_array($value, $call->categories)
        );
}

// retornos en opcion checkboxes con icono y descripcion
$icons = array();

foreach ($this['icons'] as $id=>$icon) {
    $rewards["icon-{$icon->id}"] =  array(
        'name'  => "icons[]",
        'value' => $icon->id,
        'type'  => 'checkbox',
        'class' => "icon {$icon->id}",
        'label' => $icon->name,
        'hint'  => $icon->description,
        'id'    => "icon-{$icon->id}",
        'checked' => in_array($id, $call->icons)
    );
}


$scope = array();

foreach ($this['scope'] as $value => $label) {
    $scope[] =  array(
        'value'     => $value,
        'label'     => $label
        );
}


?>
<script type="text/javascript" src="<?php echo SRC_URL; ?>/view/js/ckeditor/ckeditor.js"></script>

<form method="post" action="/dashboard/translates/overview/save" class="project" enctype="multipart/form-data">
<?php echo SuperForm::get(array(
    'autoupdate'    => false,
    'level'         => 3,
    'action'        => '',
    'method'        => 'post',
    'title'         => '',
    'hint'          => Text::get('guide-call-description'),
    'class'         => 'aqua',
    'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save-overview',
            'label' => Text::get('regular-save'),
            'class' => 'next'
        )
    ),
    'elements'      => array(
        'process_overview' => array (
            'type' => 'hidden',
            'value' => 'overview'
        ),

        'name-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('call-field-name'),
            'html'     => $original->name
        ),
        'name' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'value'     => $call->name,
            'hint'      => Text::get('tooltip-call-name'),
            'errors'    => array(),
            'ok'        => array()
        ),

        'subtitle-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('call-field-subtitle'),
            'html'     => $original->subtitle
        ),
        'subtitle' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'value'     => $call->subtitle,
            'hint'      => Text::get('tooltip-call-subtitle'),
            'errors'    => array(),
            'ok'        => array()
        ),

        'description-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('call-field-description'),
            'html'     => nl2br($original->description)
        ),
        'description' => array(
            'type'      => 'textarea',
            'title'     => '',
            'class'     => 'ckeditor-text',
            'html'      => '1',
            'hint'      => Text::get('tooltip-call-description'),
            'value'     => $call->description,
            'errors'    => array(),
            'ok'        => array()
        ),
        'description_group' => array(
            'type' => 'group',
            'children'  => array(
                'whom-orig' => array(
                    'type'      => 'html',
                    'title'     => Text::get('call-field-whom'),
                    'html'     => nl2br($original->whom)
                ),
                'whom' => array(
                    'type'      => 'textarea',
                    'title'     => '',
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-call-whom'),
                    'errors'    => array(),
                    'ok'        => array(),
                    'value'     => $call->whom
                ),
                'apply-orig' => array(
                    'type'      => 'html',
                    'title'     => Text::get('call-field-apply'),
                    'html'     => nl2br($original->apply)
                ),
                'apply' => array(
                    'type'      => 'textarea',
                    'title'     => '',
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-call-apply'),
                    'errors'    => array(),
                    'ok'        => array(),
                    'value'     => $call->apply
                ),

                'legal-orig' => array(
                    'type'      => 'html',
                    'title'     => Text::get('call-field-legal'),
                    'html'     => nl2br($original->legal)
                ),
                'legal' => array(
                    'type'      => 'textarea',
                    'title'     => '',
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-call-legal'),
                    'errors'    => array(),
                    'ok'        => array(),
                    'value'     => $call->legal
                ),
                'resources-orig' => array(
                    'type'      => 'html',
                    'title'     => Text::get('call-field-resources'),
                    'html'     => nl2br($original->resources)
                ),
                'resources' => array(
                    'type'      => 'textarea',
                    'title'     => '',
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-call-resources'),
                    'errors'    => array(),
                    'ok'        => array(),
                    'value'     => $call->resources
                )
            )
        ),

        'dossier-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('call-field-dossier'),
            'html'     => $original->dossier
        ),
        'dossier' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'hint'      => Text::get('tooltip-call-dossier'),
            'errors'    => array(),
            'ok'        => array(),
            'value'     => $call->dossier
        ),


        'tweet-orig' => array(
            'type'      => 'html',
            'title'     => Text::get('call-field-tweet'),
            'html'     => $original->tweet
        ),
        'tweet' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'hint'      => Text::get('tooltip-call-tweet'),
            'errors'    => array(),
            'ok'        => array(),
            'value'     => $call->tweet
        ),


    )

));
?>
</form>
