<?php
$this->layout('dashboard/project/layout');
?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">
        <h1>Items de Impacto para el Dato de Impacto -> <?= $this->impactData->title ?></h1>

        <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['impactProjectId', 'impactItemName', 'impactValue', 'actions'])]) ?>
    </div>
</div>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<?php $this->append() ?>
