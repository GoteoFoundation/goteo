<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content') ?>

<?= Goteo\Core\View::get('project/report.html.php',  [
    'project' => $this->project,
    'account' => $this->account,
    'contract' => $this->contract,
    'Data' => $this->data,
    'admin' => true
]) ?>

<?php $this->replace() ?>

