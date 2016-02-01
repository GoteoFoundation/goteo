<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content') ?>

<?php

$project = $this->project;

$elements = array(
    'created' => array(
        'title'     => 'Fecha de creación',
        'value'     => !empty($project->created) ? $project->created : null
    ),
    'updated' => array(
        'title'     => 'Fecha de enviado a revisión',
        'value'     => !empty($project->updated) ? $project->updated : null
    ),
    'published' => array(
        'title'     => 'Fecha de inicio de campaña',
        'subtitle'  => '(Segun esta fecha se calculan los días)',
        'value'     => !empty($project->published) ? $project->published : null
    ),
    'passed' => array(
        'title'     => 'Fecha de paso a segunda ronda',
        'subtitle'  => '(marca fin de primera ronda)',
        'value'     => !empty($project->passed) ? $project->passed : null
    ),
    'success' => array(
        'title'     => 'Fecha de éxito',
        'subtitle'  => '(marca fin de segunda ronda)',
        'value'     => !empty($project->success) ? $project->success : null
    ),
    'closed' => array(
        'title'     => 'Fecha de cierre',
        'value'     => !empty($project->closed) ? $project->closed : null
    )

);
?>

<p>
    <?php if (!empty($project->passed)) {
        echo 'El proyecto terminó la primera ronda el día <strong>'.date('d/m/Y', strtotime($project->passed)).'</strong>.';
        if ($project->passed != $project->willpass) {
            echo '<br />Aunque debería haberla terminado el día <strong>'.date('d/m/Y', strtotime($project->willpass)).'</strong>.';
        }
    } else {
        echo 'El proyecto terminará la primera ronda el día <strong>'.date('d/m/Y', strtotime($project->willpass)).'</strong>.';
    } ?>

</p>

    <p>Cambiar las fechas puede causar cambios en los días de campaña del proyecto.</p>

    <form method="post" action="/admin/projects/dates/<?php echo $project->id ?>" >

<?php foreach ($elements as $id=>$element) : ?>
    <div id="<?php echo $id ?>">
        <h4><?php echo $element['title'] ?>:</h4>

        <?= $this->html('input', ['value' => $element['value'], 'name' => $id, 'attribs' => ['id'=>$id, 'class' => 'datepicker']]) ?>

        <?php if (!empty($element['subtitle'])) echo $element['subtitle'].'<br />'; ?>
    </div>
        <br />
<?php endforeach ?>

        <input type="submit" name="save-dates" value="Guardar" />

    </form>

<?php $this->replace() ?>
