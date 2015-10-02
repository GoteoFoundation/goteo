<?php

use Goteo\Model;

$original = $this->original;
$user     = $this->user;
$project  = $this->project;


// Lastima que no me sirve ni el getAll ni el getList ni el Published
$projects = array();
$query = Model\Project::query("
    SELECT
        project.id as id,
        project.name as name
    FROM    project
    WHERE status = 3
    ORDER BY project.name ASC
    ");

foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $item) {
    $projects[$item->id] = $item->name;
}


$this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<div class="widget">
    <p>Movemos el aporte de <strong><?php echo $user->name ?></strong> al proyecto <strong><?php echo $project->name; ?></strong> de <strong><?php echo $original->amount; ?> &euro;</strong> mediante <strong><?php echo $original->method; ?></strong> del d&iacute;a <strong><?php echo date('d-m-Y', strtotime($original->invested)); ?></strong>.</p>
    <form id="filter-form" action="/admin/accounts/move/<?php echo $original->id ?>" method="post">
        <p>
            <label for="invest-project">Al proyecto:</label><br />
            <select id="invest-project" name="project">
                <option value="">Seleccionar el proyecto al que se mueve el aporte</option>
            <?php foreach ($projects as $projectId=>$projectName) :
                if ($projectId == $original->project) continue; ?>
                <option value="<?php echo $projectId; ?>"><?php echo $projectName; ?></option>
            <?php endforeach; ?>
            </select>
        </p>

        <input type="submit" name="move" value="Reubicar"
            onclick="return confirm('El aporte original va a desaparecer de los cofinanciadores y no se va a tratar automaticamente al final de ronda, ¿Seguimos?');" />

    </form>
</div>

<?php $this->replace() ?>
