<?php

$this->layout('admin/announcements/layout');

$this->section('admin-search-box-addons');
?>
<a class="btn btn-cyan" href="/admin/announcement"><i class="fa fa-arrow-circle-left"></i> <?= $this->text('admin-back-list') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

<?php
$id = $this->announcement->id;
?>

<h4 class="title"><?= $id ? $this->text('admin-announcement-edit', "#{$id}") : $this->text('admin-announcement-add') ?></h4>


<?= $this->form_form($this->raw('form')) ?>

</div>
</div>

<?php $this->replace() ?>
