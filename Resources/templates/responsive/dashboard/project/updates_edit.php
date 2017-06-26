<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

  <div class="dashboard-content">

    <h2><?= $this->post->title ?></h2>

    <?php if($this->errorMsg): ?>
        <div class="alert alert-info"><?= $this->errorMsg ?></div>
    <?php endif ?>

<?php //print_r($this->post) ?>

<form action="<?= $this->get_uri() ?>" method="post">
<?php  // echo $this->insert('partials/forms/standard', ['form' => $this->form]) ?>

<?= $this->form_form($this->raw('form')) ?>

<button type="submit" class="btn btn-green"><?= $this->text('regular-submit') ?></button>
</form>

</div>

<?php $this->replace() ?>
