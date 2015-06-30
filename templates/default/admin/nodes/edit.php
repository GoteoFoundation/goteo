<?php $this->layout('admin/nodes/layout') ?>

<?php $this->section('admin-node-content') ?>

    <form method="post" action="/admin/nodes/edit/<?= $this->node->id ?>" >

    <?= $this->insert('admin/nodes/partials/edit_common', ['masternode' => $this->node->isMasterNode()]) ?>

        <input type="submit" name="save" value="Guardar" />
    </form>

<?php $this->replace() ?>
