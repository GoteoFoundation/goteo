<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content') ?>

    <form method="post" action="/admin/projects/assign/<?= $this->project->id ?>" >

    <p>
        <label for="call-filter">Asignarlo a la convocatoria:</label><br />
        <select id="call-filter" name="call" >
        <?php foreach ($this->available as $callId => $callName) : ?>
            <option value="<?= $callId ?>"><?= $callName ?></option>
        <?php endforeach; ?>
        </select>
    </p>

        <input type="submit" name="save" value="Aplicar" />
    </form>

<?php $this->replace() ?>
