<?php

$this->layout('admin/faq/layout');

$this->section('admin-container-head');

?>

<?php $this->append() ?>


<?php $this->section('admin-container-body') ?>

  <?= $this->form_form($this->raw('form')) ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<?php $this->append() ?>
