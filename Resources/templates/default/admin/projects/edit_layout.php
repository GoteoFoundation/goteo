<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<a href="/admin/projects" class="button">Cancelar</a>
&nbsp;&nbsp;&nbsp; <a href="/project/<?= $this->project->id; ?>" class="button" target="_blank">Ver proyecto</a>
&nbsp;&nbsp;&nbsp; <a href="/project/edit/<?= $this->project->id; ?>" class="button" target="_blank">Editar proyecto</a>
&nbsp;&nbsp;&nbsp; <a href="/admin/projects/images/<?= $this->project->id; ?>" class="button">Imágenes</a>

<div class="widget">

    <?= $this->supply('admin-project-content') ?>

</div>

<?php $this->replace() ?>
