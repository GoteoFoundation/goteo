<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm;

define('ADMIN_NOAUTOSAVE', true);

$promo = $this['promo'];

switch ($this['action']) {
    case 'add':
        $title = "Añadiendo nuevo proyecto destacado";

        $availables = array();

        foreach ($this['projects'] as $project) {
            $availables[] = array(
                'value' => $project->id,
                'label' => $project->name . ' ('. $this['status'][$project->status] . ')'
            );
        }

        $project = array(
            'title'     => 'Proyecto',
            'class'     => 'inline',
            'type'      => 'radios',
            'options'   => $availables,
            'value'     => $promo->project,
            'hint'      => 'Seleccionar el proyecto a destacar',
        );
    break;
    case 'edit':
        $title = "Editando el proyecto destacado: '{$promo->name}'";

        $project = array (
            'type' => 'hidden',
            'value' => $promo->project
        );
    break;
}

?>

<form method="post" action="/admin/promote">
<?php
echo new SuperForm(array(

    'level'         => 3,
    'method'        => 'post',
    'title'         => $title,
    'hint'          => "Los proyectos destacados aparecen en la portada, en el módulo 'Destacados'",
    'footer'        => array(
        'save' => array(
            'type'  => 'submit',
            'label' => 'Guardar',
            'class' => 'button',
            'name'  => 'save'
        )
    ),
    'elements'      => array(
        'action' => array(
            'type' => 'hidden',
            'value' => $this['action']
        ),

        'order' => array(
            'type' => 'hidden',
            'value' => $promo->order
        ),

        'project' => $project,

        'title' => array(
            'type'      => 'textbox',
            'required'  => true,
            'size'      => 20,
            'title'     => 'Título',
            'hint'      => 'Título conceptual del proyecto destacado',
            'errors'    => !empty($promo->title) ? array('Pon un título al proyecto destacado') : array(),
            'value'     => $promo->title
        ),

        'description' => array(
            'type'  => 'textbox',
            'required'  => true,
            'title' => 'Descripción',
            'size' => 100,
            'maxlength' => 100,
            'hint'  => 'Máximo 100 caracteres',
            'errors'    => array(),
            'value' => $promo->description
        )
    )

));
?>

</form>