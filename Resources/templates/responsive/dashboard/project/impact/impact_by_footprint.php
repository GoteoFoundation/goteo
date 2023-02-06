<?php
    $this->layout('dashboard/project/layout');
?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">
        <h1>Datos de impacto del proyecto</h1>

        <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->impactDataProject, ['impactDataId', 'estimation', 'dataValue', 'actions'])]) ?>
    </div>
</div>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<?php $this->append() ?>
