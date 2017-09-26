<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1><?= $this->text('dashboard-translate-project') ?></h1>

    <?= $this->insert('dashboard/project/translate/partials/tabs', ['zones' => $this->zones]) ?>

    <?= $this->supply('dashboard-translate-project') ?>

  </div>
</div>

<?php $this->replace() ?>
