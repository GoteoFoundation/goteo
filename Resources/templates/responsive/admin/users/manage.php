<?php

$this->layout('admin/layout');

$this->section('admin-content');
?>
<div class="admin-content">
  <div class="inner-container">
    <h2><?= $this->text('admin-users') ?></h2>
    <?php print_r($this->user) ?>
  </div>
</div>
<?php $this->replace() ?>
