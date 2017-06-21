<?php $this->layout('dashboard/layout') ?>

<?php $this->section('sidebar-header') ?>

<?= $this->insert('project/widgets/mini', ['project' => $this->project, 'admin' => $this->project->userCanEdit($this->get_user())]) ?>

<?php $this->replace() ?>
