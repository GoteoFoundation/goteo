<?php
$this->layout('dashboard/project/layout');
?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">
        <h1><?= $this->text('dashboard-project-impact-items-title', $this->impactData->title) ?></h1>

        <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['impactProjectId', 'impactItemName', 'impactValue', 'actions'])]) ?>
        <a class="btn btn-cyan" href="/dashboard/project/<?= $this->project->id ?>/impact/impact_item/costs/add"><i class="fa fa-plus"></i> <?= $this->text('regular-add') ?></a>
    </div>
</div>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<?php $this->append() ?>
