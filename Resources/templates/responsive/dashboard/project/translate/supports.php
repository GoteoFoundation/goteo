<?php $this->layout('dashboard/project/translate/layout') ?>

<?php $this->section('dashboard-translate-project') ?>
<?php

$form = $this->raw('form');

?>
  <?= $this->form_start($form) ?>

  <?php foreach($this->supports as $support): ?>
      <div class="panel section-content" data-id="<?= $support->id ?>">
        <div class="panel-body">
            <?= $this->form_row($form['support_' . $support->id]); ?>
            <?= $this->form_row($form['description_' . $support->id]) ?>
        </div>
      </div>
  <?php endforeach ?>

  <?= $this->form_end($form) ?>

<?php $this->replace() ?>


