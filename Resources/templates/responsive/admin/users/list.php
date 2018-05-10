<?php

$this->layout('admin/users/layout');

$this->section('admin-search-box-addons');
?>
<a class="btn btn-cyan pull-right" href="/admin/users/add"><i class="fa fa-plus"></i> <?= $this->text('admin-users-add') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

    <h5><?= $this->text('admin-list-total', $this->total) ?></h5>

    <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list)]) ?>

  </div>
</div>

<?php $this->replace() ?>


