<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <?php if($this->post->id): ?>
      <h2>
        <?php if($this->exit_link): ?>
          <?= $this->text('dashboard-project-updates-translating') ?>
        <?php else: ?>
          <?= $this->text('dashboard-project-updates-editing') ?>
        <?php endif ?>

        <?php if($this->languages): ?>
          <?= $this->insert('dashboard/partials/translate_menu', ['class' => 'pull-right', 'base_link' => '/dashboard/project/' . $this->project->id . '/updates/' . $this->post->id . '/', 'lang' => $this->lang]) ?>
        <?php endif ?>
      </h2>
      <h5>#<?= $this->post->id ?> "<?= $this->post->title ?>"</h5>
    <?php else: ?>
        <h2><?= $this->text('dashboard-project-updates-add') ?></h2>
    <?php endif ?>

    <?php if($this->errorMsg): ?>
        <div class="alert alert-info"><?= $this->errorMsg ?></div>
    <?php endif ?>


    <?= $this->form_form($this->raw('form')) ?>


  </div>
</div>

<?php $this->replace() ?>
