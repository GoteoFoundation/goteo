<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2>
        <?= $this->text('dashboard-menu-projects-updates') ?>
    </h2>

    <p><?= $this->text('guide-project-updates') ?></p>


    <blockquote>
        <small><?= $this->text('dashboard-project-blog-wrongstatus') ?></small>
        <?= $this->text('dashboard-project-not-alive-yet') ?>
    </blockquote>


  </div>
</div>

<?php $this->replace() ?>
