<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <h2><?= $this->post->title ?></h2>

    <?php if($this->errorMsg): ?>
        <div class="alert alert-info"><?= $this->errorMsg ?></div>
    <?php endif ?>


    <?= $this->form_form($this->raw('form')) ?>


  </div>
</div>

<?php $this->replace() ?>
