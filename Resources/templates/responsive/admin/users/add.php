<?php

$this->layout('admin/users/layout');

$this->section('admin-search-box-addons');
?>
<a class="btn btn-cyan pull-right" href="/admin/users?<?= $this->get_querystring() ?>"><i class="fa fa-arrow-circle-left"></i> <?= $this->text('admin-back-list') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

    <h4 class="title"><?= $this->text('admin-users-add') ?></h4>


    <?= $this->form_form($this->raw('form')) ?>

  </div>
</div>

<?php $this->replace() ?>
