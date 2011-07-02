<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

echo new View ('view/dashboard/projects/selector.html.php', $this);

$project = $this['project'];
$errors = $project->errors['supports'] ?: array();
$okeys  = $project->okeys['supports'] ?: array();
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

    // a ver si es el que estamos editando o no
    if (isset($_POST["support-{$support->id}-edit"])) {
        // a este grupo le ponemos estilo de edicion
    $supports["support-{$support->id}"] = array(
            'type'      => 'group',
                'class'     => 'support edit',
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
                    'hint'      => Text::get('tooltip-project-support-type'),
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

    } else {
        // a este grupo lo ponemos cerrado, en html y boton para ir a editarlo
        // ese boton lanza el formulario igual que lo hace el de añadir, quitar o aceptar
        // a ver si el tipo de registro lo podemos poner con el icono
        // y el boton de editar en la misma linea
        $supports["support-{$support->id}"] = array(
                'type'      => 'group',
                'class'     => 'support line',
                'children'  => array(
                    "support-{$support->id}-support" => array(
                        'type'      => 'html',
                        'class'     => 'inline',
                        'html'      => $this['types'][$support->type] . ': ' . $support->support
                    ),
                    "support-{$support->id}-edit" => array(
                        'type'  => 'submit',
                        'label' => Text::get('form-edit-button'),
                        'class' => 'inline edit support-edit'
                    )
                )
            );

    }


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
?>
</form>