<?php

$this->layout('admin/layout');

if($this->is_pronto() && ($this->is_ajax() && $this->has_query('ajax') || ($this->debug() && $this->has_query('ajax')))) {

    $this->section('admin-content');
        echo $this->supply('admin-container-body');
    $this->replace();
    return;
}


$this->section('admin-content');
?>
<div class="admin-content">
  <div class="inner-container">
    <?= $this->supply('admin-container-head') ?>
    <?= $this->supply('admin-container-body') ?>
  </div>
</div>
<?php $this->replace() ?>
