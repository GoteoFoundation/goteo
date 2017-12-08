<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1>
        <?= $this->text('dashboard-translate-profile') ?>
        <?= $this->insert('dashboard/partials/translate_menu', ['class' => 'pull-right', 'base_link' => '/dashboard/settings/profile/', 'exit_link' => '/dashboard/settings/profile', 'lang' => $this->current]) ?>
    </h1>

    <p><?= $this->text('dashboard-translate-profile-desc', ['%LANG%' => '<strong><em>' . $this->languages[$this->current] . '</em></strong>', '%URL%' => '/dashboard/settings/profile']) ?></p>

    <?= $this->supply('dashboard-content-form', function() {return $this->form_form($this->raw('form'));}) ?>


  </div>
</div>

<?php $this->replace() ?>
