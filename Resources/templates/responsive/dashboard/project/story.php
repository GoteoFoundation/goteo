<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">


    <h1>
    	<?= $this->text('dashboard-story-form-title') ?>
    	<?= $this->insert('dashboard/partials/translate_menu', ['class' => 'pull-right', 'base_link' => '/dashboard/project/'.$project->id.'/translate/story', 'lang' => $this->current]) ?>

    </h1>
    <div class="auto-hide">
        <div class="inner"><?= $this->text('dashboard-story-guide-description') ?></div>
        <div class="more"><i class="fa fa-info-circle"></i> <?= $this->text('regular-help') ?></div>
    </div>

    <?= $this->insert('dashboard/project/partials/goto_first_error') ?>

    <?= $this->supply('dashboard-content-form', function() {return $this->form_form($this->raw('form'));}) ?>

  </div>
</div>

<?php $this->replace() ?>
