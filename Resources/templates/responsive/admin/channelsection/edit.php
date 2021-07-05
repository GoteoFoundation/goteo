<?php

$this->layout('admin/channelsection/layout');

$this->section('admin-search-box-addons');
?>
<a class="btn btn-cyan" href="/admin/channelsection/<?= $this->current_node ?>"><i class="fa fa-arrow-circle-left"></i> <?= $this->text('admin-back-list') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

    <?= $this->form_form($this->raw('form')) ?>

  </div>
</div>

<?php $this->replace() ?>
