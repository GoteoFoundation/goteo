<?php
$this->layout('dashboard/project/layout');
$form = $this->raw('form');
?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">
        <h1>Items de Impacto para el Dato de Impacto -> <?= $this->impactData->title ?></h1>

        <?= $this->form_form($form) ?>
    </div>
</div>


<?php $this->replace() ?>

<?php $this->section('footer') ?>

<?php $this->append() ?>
