<?php $this->layout('admin/layout') ?>
<?php $this->section('admin-content') ?>

<?php if($this->template === 'admin/node/edit'): ?>
    <a href="/admin/node" class="button">Cancelar</a>
<?php else: ?>
    <a href="/admin/node/edit" class="button">Editar</a>
<?php endif ?>
<?php if($this->translator): ?>
&nbsp;&nbsp;&nbsp; <a href="/translate/node/<?php echo $this->node->id ?>/data/edit" class="button">Traducir</a>
<?php endif ?>
<?php if($this->superadmin): ?>
&nbsp;&nbsp;&nbsp; <a href="/admin/node/admins" class="button">Ver administradores</a>
<?php endif ?>
	
&nbsp;&nbsp;&nbsp;<a href="/channel/<?= $this->node->id ?>" class="button" target="_blank">Preview</a>

<div class="widget">

    <?= $this->supply('admin-node-content') ?>

</div>

<?php $this->replace() ?>
