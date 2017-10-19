<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <h1><?= $this->text('dashboard-menu-profile-personal')?></h1>
    <p><?= $this->text('guide-dashboard-user-personal') ?></p>

    <?= $this->supply('dashboard-content-form', function() {return $this->form_form($this->raw('form'));}) ?>


  </div>
</div>

<?php $this->replace() ?>
