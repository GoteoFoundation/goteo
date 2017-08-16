<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">

    <h1><?= $this->text('dashboard-menu-projects-analytics') ?></h1>

    <?= $this->form_form($this->raw('form')) ?>

</div>

<?php $this->replace() ?>
