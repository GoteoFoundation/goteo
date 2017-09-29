<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <h1><?= $this->text('overview-main-header') ?></h1>
    <p><?= $this->text('guide-project-description') ?></p>

    <?= $this->form_form($this->raw('form')) ?>


  </div>
</div>

<?php $this->replace() ?>
