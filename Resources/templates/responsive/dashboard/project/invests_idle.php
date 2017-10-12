<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2><?= $this->text('dashboard-menu-projects-rewards') ?></h2>

    <p><?= $this->text('guide-project-invests') ?></p>


    <blockquote>
        <?= $this->text('dashboard-project-not-alive-yet') ?>
    </blockquote>


  </div>
</div>

<?php $this->replace() ?>
