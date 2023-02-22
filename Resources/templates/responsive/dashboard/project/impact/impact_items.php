<?php
$this->layout('dashboard/project/layout');
?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">
        <h1><?= $this->text('dashboard-project-impact-items-title', $this->impactData->title) ?></h1>
        <p><?= $this->t('dashboard-project-impact-items-subtitle') ?></p>
        <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['impactItemName', 'impactValue', 'actions'])]) ?>
        <a class="btn btn-cyan" href="/dashboard/project/<?= $this->project->id ?>/impact/impact_data/<?= $this->impactData->id ?>/impact_item/add"><i class="fa fa-plus"></i> <?= $this->text('regular-add') ?></a>
    </div>
</div>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<?php $this->append() ?>
