<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1><?= $this->text('dashboard-translate-' . ($this->step ? $this->step : 'project')) ?></h1>

    <?= $this->supply('dashboard-translate-tabs', $this->insert('dashboard/project/translate/partials/tabs', ['zones' => $this->zones])) ?>

    <?= $this->supply('dashboard-translate-project') ?>

  </div>
</div>

<?php $this->replace() ?>
