<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="container">
    <h1><?= $this->t('dashboard-project-pitch-title') ?></h1>

    <div class="row">
        <div class="col-md-10 col-sm-10 description">
            <?= $this->t('dashboard-project-pitch-description') ?>
        </div>
    </div>

    <?= $this->insert('dashboard/project/partials/pitch', [
        'pitches' => $this->pitches,
        'display' => 1,
        'pitch' => 1,
        'hide_spheres' => true,
        'hide_projects' => true
        ]) ?>
  </div>
</div>

<?php $this->replace() ?>