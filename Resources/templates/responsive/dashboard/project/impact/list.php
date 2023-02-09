<?php
$this->layout('dashboard/project/layout');
?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">

        <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->impactDataProject, ['impactDataId', 'estimation', 'dataValue', 'actions'])]) ?>
        <a class="btn btn-cyan" href="/dashboard/project/<?= $this->project->id ?>/impact/<?= $this->footprint->id ?>/impact_data/add"><i class="fa fa-plus"></i> <?= $this->text('regular-add') ?></a>
    </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<?php $this->append() ?>
