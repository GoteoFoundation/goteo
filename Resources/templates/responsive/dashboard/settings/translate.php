<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1>
        <?= $this->text('dashboard-menu-profile-profile') ?>
        <?= $this->insert('dashboard/partials/translate_menu', ['class' => 'pull-right', 'base_link' => '/dashboard/settings/profile/']) ?>
    </h1>

    <p><?= $this->text('dashboard-translate-profile-desc', ['%LANG%' => '<strong><em>' . $this->languages[$this->current] . '</em></strong>', '%URL%' => '/dashboard/settings/profile']) ?></p>


  </div>
</div>

<?php $this->replace() ?>
