<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php if($this->template === 'admin/nodes/edit'): ?>
    <a href="/admin/nodes" class="button">Cancelar</a>
<?php else: ?>
    <a href="/admin/nodes/edit" class="button">Editar</a>
<?php endif ?>

<div class="widget">

    <?= $this->supply('admin-node-content') ?>

</div>

<?php $this->replace() ?>
