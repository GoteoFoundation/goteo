<?php $this->layout('admin/nodes/layout') ?>

<?php $this->section('admin-node-content') ?>

    <form method="post" action="/admin/nodes/add" >
    <p>
        <label for="node-id">Identificador:</label><br />
        <input type="text" id="node-id" name="id" value="" />
    </p>

    <?= $this->insert('admin/nodes/partials/edit_common') ?>


        <input type="submit" name="save" value="Crear" />
    </form>

<?php $this->replace() ?>
