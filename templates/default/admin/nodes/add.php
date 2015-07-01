<?php $this->layout('admin/nodes/layout') ?>

<?php $this->section('admin-node-content') ?>

    <form method="post" action="/admin/nodes/add" >
    <p>
        <label for="node-id">Identificador:</label><br />
        <input style="border:1px solid #b00" type="text" id="node-id" name="id" value="<?= $this->node->id ?>" />
    </p>

    <?= $this->insert('admin/nodes/partials/edit_common') ?>


        <input type="submit" name="save" value="Crear" />
    </form>

<?php $this->replace() ?>
