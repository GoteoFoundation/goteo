<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<div class="widget board" style="width:350px; float:left; margin-right: 5px;">
    <?= $this->insert('admin/home/partials/side') ?>
</div>

<div class="widget board" style="width:350px; float:left; margin-right: 5px;">
    <?= $this->insert('admin/home/partials/central') ?>
</div>


<?php $this->replace() ?>
