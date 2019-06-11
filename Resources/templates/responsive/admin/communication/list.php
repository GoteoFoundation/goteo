<?php

$this->layout('admin/communication/layout');

$this->section('admin-search-box-addons');
?>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

<?= $this->form_form($this->raw('form')) ?>

  </div>
</div>

<?php $this->replace() ?>