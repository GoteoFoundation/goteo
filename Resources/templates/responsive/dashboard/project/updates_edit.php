<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <?php if($this->post->id): ?>
      <?php if($this->exit_link): ?>
        <h2><?= $this->text('dashboard-project-updates-translating') ?> #<?= $this->post->id ?></h2>
        <blockquote class="padding-right"><?= $this->text('dashboard-project-updates-translating-post', ['%LANG%' => '<strong><em>' . $this->languages[$this->lang] . '</em></strong>', '%ORIGINAL%' => '<strong><em>' . $this->languages[$this->project->lang] . '</em></strong>']) ?>
          <?php if($this->languages): ?>
            <?= $this->insert('dashboard/partials/translate_menu', ['class' => 'pull-right', 'base_link' => '/dashboard/project/' . $this->project->id . '/updates/' . $this->post->id . '/', 'lang' => $this->lang, 'percentModel' => $this->post]) ?>
          <?php endif ?>
        </blockquote>

      <?php else: ?>
        <h2><?= $this->text('dashboard-project-updates-editing') ?> #<?= $this->post->id ?>
            <?php if($this->languages): ?>
              <?= $this->insert('dashboard/partials/translate_menu', ['class' => 'pull-right', 'base_link' => '/dashboard/project/' . $this->project->id . '/updates/' . $this->post->id . '/', 'lang' => $this->lang, 'percentModel' => $this->post]) ?>
            <?php endif ?>
        </h2>
      <?php endif ?>

    <?php else: ?>
        <h2><?= $this->text('dashboard-project-updates-add') ?></h2>
    <?php endif ?>

    <?php if($this->errorMsg): ?>
        <div class="alert alert-info"><?= $this->errorMsg ?></div>
    <?php endif ?>


    <?= $this->supply('dashboard-content-form', function() {return $this->form_form($this->raw('form'));}) ?>


  </div>
</div>

<?php $this->replace() ?>
