<?php

$this->layout('admin/communication/layout');

$this->section('admin-search-box-addons');

?>

<a class="btn btn-cyan" href="/admin/communication/add"><i class="fa fa-plus"></i> <?= $this->text('admin-communications-add') ?></a>

<?php $this->replace() ?>


<?php $this->section('admin-container-body') ?>

  <h5><?= $this->text('admin-list-total', $this->total) ?></h5>

  <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['id', 'subject', 'image', 'date', 'template', 'type', 'langs', 'success', 'filter', 'actions'])]) ?>

  </div>
</div>

<?php $this->replace() ?>