<?php $this->layout('dashboard/project/translate/layout') ?>

<?php $this->section('dashboard-translate-project') ?>

  <?= $this->form_start($this->raw('form')) ?>

  <?php foreach($this->costs as $cost): ?>
     <?= $this->insert('dashboard/project/translate/partials/cost_item', ['cost' => $cost, 'form' => $this->raw('form')]) ?>
  <?php endforeach ?>

  <?= $this->form_end($this->raw('form')) ?>

<?php $this->replace() ?>

