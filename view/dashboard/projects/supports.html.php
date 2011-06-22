<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

echo new View ('view/dashboard/projects/selector.html.php', $this);

$project = $this['project'];
$errors = $this['errors'];
$this['level'] = 3;

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
                    'title'     => 'Resumen',
                    'type'      => 'textbox',
                    'size'      => 100,
                    'class'     => 'inline',
                    'value'     => $support->support,
                    'hint'      => Text::get('tooltip-project-support-support'),
                ),
                "support-{$support->id}-type" => array(
                    'title'     => 'Tipo',
                    'class'     => 'inline support-type',
                    'type'      => 'radios',
                    'options'   => $support_types,
                    'value'     => $support->type,
                    'hint'      => Text::get('tooltip-project-support-type'),
                ),
                "support-{$support->id}-description" => array(
                    'type'      => 'textarea',
                    'title'     => 'Descripción',
                    'cols'      => 100,
                    'rows'      => 4,
                    'class'     => 'inline',
                    'hint'      => Text::get('tooltip-project-support-description'),
                    'value'     => $support->description
                ),
                "support-{$support->id}-remove" => array(
                    'type'  => 'submit',
                    'label' => 'Quitar',
                    'class' => 'inline remove support-remove'
                )
            )
        );
}
?>

<form method="post" action="/dashboard/projects/supports/save" class="project" enctype="multipart/form-data">

<?php echo new SuperForm(array(

    'action'        => '',
    'level'         => $this['level'],
    'method'        => 'post',
    'title'         => '',
    'hint'          => Text::get('guide-project-supports'),
    'class'         => 'aqua',
    'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save-supports',
            'label' => 'Guardar',
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
            'title'     => 'Colaboraciones',
            'hint'      => Text::get('tooltip-project-supports'),
            'children'  => $supports + array(
                'support-add' => array(
                    'type'  => 'submit',
                    'label' => 'Añadir',
                    'class' => 'add support-add',
                )
            )
        )
    )

));
?>
</form>