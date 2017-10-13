<?php $this->layout('dashboard/' . ($this->section === 'projects' ? 'project/' : '') . 'layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1>
        <h1><?= $this->section === 'projects' ? '1. ' . $this->text('profile-about-header') : $this->text('dashboard-menu-profile-profile') ?></h1>
        <?= $this->insert('dashboard/partials/translate_menu', ['class' => 'pull-right', 'base_link' => '/dashboard/settings/profile/', 'lang' => $this->current]) ?>
    </h1>
    <p><?= $this->text('guide-dashboard-user-profile') ?></p>

    <?= $this->supply('dashboard-content-form', function() {return $this->form_form($this->raw('form'));}) ?>

  </div>
</div>

<?php $this->replace() ?>
