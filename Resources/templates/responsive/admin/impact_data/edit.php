<?php

$this->layout('admin/impact_data/layout');

$this->section('admin-container-head');

?>

<?php $this->append() ?>

<?php $this->section('admin-container-body') ?>

  <?= $this->form_form($this->raw('form')) ?>

  </div>
</div>

<?php $this->replace() ?>