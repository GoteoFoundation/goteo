<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <h1><?= $this->text('dashboard-menu-profile-preferences')?></h1>


    <?= $this->form_form($this->raw('form')) ?>


  </div>
</div>

<?php $this->replace() ?>
