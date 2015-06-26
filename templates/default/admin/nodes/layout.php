<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<a href="/admin/nodes" class="button">Cancelar</a>

<?php if($this->template === 'admin/nodes/edit'): ?>
    <a href="/admin/nodes/admins/<?= $this->node->id ?>" class="button">Admins</a>
<?php elseif($this->template === 'admin/nodes/admins'): ?>
    <a href="/admin/nodes/edit/<?= $this->node->id ?>" class="button">Editar</a>
<?php endif ?>
<?php if(in_array($this->template, ['admin/nodes/edit', 'admin/nodes/admins']) && !$this->node->isMasterNode()): ?>
    <a href="/admin/node?admin_node=<?= $this->node->id ?>" class="button">Editar como admin de nodo</a>
<?php endif ?>

<div class="widget">

    <?= $this->supply('admin-node-content') ?>

</div>

<?php $this->replace() ?>
