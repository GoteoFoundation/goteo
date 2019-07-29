<?php

$this->layout('admin/workshop/layout');

$this->section('admin-search-box-addons');
?>
<a class="btn btn-cyan" href="/admin/workshop/add"><i class="fa fa-plus"></i> <?= $this->text('admin-workshop-add') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

    <h5><?= $this->text('admin-list-total', $this->total) ?></h5>

    <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['id', 'title', 'date', 'city', 'actions'])]) ?>

  </div>
</div>

<?php $this->replace() ?>


