<?php $form = $this->raw('form'); ?>
<?php $this->layout('dashboard/layout') ?>
<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">
        <strong><?= $this->t('subscription-cancel-description') ?></strong>
        <?= $this->form_start($form); ?>
        <?= $this->form_row($form["remove"], [], true) ?>
        <?= $this->form_end($form); ?>
    </div>
</div>

<?php $this->replace() ?>