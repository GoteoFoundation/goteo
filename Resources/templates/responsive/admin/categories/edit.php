<?php

$this->layout('admin/categories/layout');

$this->section('admin-search-box-addons');
?>
<a class="btn btn-cyan" href="/admin/categories/<?=$this->tab?>?<?= $this->get_querystring() ?>"><i class="fa fa-arrow-circle-left"></i> <?= $this->text('admin-back-list') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

    <h4 class="title"><?= $this->instance->id ? $this->text("admin-{$this->tab}-edit", "#{$this->instance->id}") : $this->text("admin-{$this->tab}-add") ?></h4>


    <?= $this->form_form($this->raw('form')) ?>

  </div>
</div>

<?php $this->replace() ?>
