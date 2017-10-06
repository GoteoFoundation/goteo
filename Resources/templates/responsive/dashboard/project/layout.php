<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-sidebar-header') ?>

    <?= $this->insert('project/widgets/micro', ['project' => $this->project, 'admin' => $this->admin]) ?>

<?php $this->replace() ?>
