<?php

$this->layout('admin/communication/layout');

$this->section('admin-container-head');

?>

<?php $this->append() ?>


<?php $this->section('admin-container-body') ?>

<h5><?= $this->text('admin-list-total', $this->total) ?></h5>


<?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['name', 'email'])]) ?>

  </div>
</div>

<?php $this->replace() ?>