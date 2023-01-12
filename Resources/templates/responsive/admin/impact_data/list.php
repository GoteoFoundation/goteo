<?php

$this->layout('admin/impact_data/layout');

$this->section('admin-search-box-addons');

?>

<a class="btn btn-cyan" href="/admin/impactdata/add"><i class="fa fa-plus"></i> <?= $this->text('regular-add') ?></a>

<?php $this->replace() ?>


<?php $this->section('admin-container-body') ?>

  <h5><?= $this->text('admin-list-total', $this->total) ?></h5>

  <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['id', 'title', 'type', 'source', 'actions'])]) ?>

  </div>
</div>

<?php $this->replace() ?>
