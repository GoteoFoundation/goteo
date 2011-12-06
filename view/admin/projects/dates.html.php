<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\SuperForm;

define('ADMIN_NOAUTOSAVE', true);

$project = $this['project'];

if (!$project instanceof Model\Project) {
    throw new Redirection('/admin/projects');
}

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&category={$filters['category']}";


// Superform
?>
<form method="post" action="/admin/projects/<?php echo $filter ?>" class="project" enctype="multipart/form-data">

    <?php echo new SuperForm(array(

        'action'        => '',
        'level'         => 3,
        'method'        => 'post',
        'title'         => '',
        'hint'          => 'Cambiar las fechas solamente si se conocen los efectos colaterales',
        'class'         => 'aqua',
        'footer'        => array(
            'view-step-preview' => array(
                'type'  => 'submit',
                'name'  => 'save-dates',
                'label' => Text::get('regular-save'),
                'class' => 'next'
            )
        ),
        'elements'      => array(
            'id' => array (
                'type' => 'hidden',
                'value' => $project->id
            ),
            'created' => array(
                'type'      => 'datebox',
                'required'  => true,
                'title'     => 'Fecha de creación',
                'size'      => 8,
                'value'     => !empty($project->created) ? $project->created : null
            ),
            'updated' => array(
                'type'      => 'datebox',
                'required'  => true,
                'title'     => 'Fecha de enviado a revisión',
                'size'      => 8,
                'value'     => !empty($project->updated) ? $project->updated : null
            ),
            'published' => array(
                'type'      => 'datebox',
                'title'     => 'Fecha de inicio de campaña',
                'size'      => 8,
                'value'     => !empty($project->published) ? $project->published : null
            ),
            'success' => array(
                'type'      => 'datebox',
                'title'     => 'Fecha de éxito',
                'size'      => 8,
                'value'     => !empty($project->success) ? $project->success : null
            ),
            'closed' => array(
                'type'      => 'datebox',
                'title'     => 'Fecha de cierre',
                'size'      => 8,
                'value'     => !empty($project->closed) ? $project->closed : null
            ),
            'passed' => array(
                'type'      => 'datebox',
                'title'     => 'Fecha de paso a segunda ronda',
                'size'      => 8,
                'value'     => !empty($project->passed) ? $project->passed : null
            )

        )

    ));
    ?>

</form>