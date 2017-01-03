<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content');
$project = $this->project;
     ?>

    <form method="post" action="/admin/projects/location/<?= $project->id ?>" >
    <h2>Localización del proyecto</h2>
    <dl>
        <?= $this->insert('admin/partials/generic_location') ?>
    </dl>

    <input type="submit" value="<?= $this->text('regular-save') ?>">
    </form>


<?php $this->replace() ?>
