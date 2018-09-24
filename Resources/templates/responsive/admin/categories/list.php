<?php

$this->layout('admin/categories/layout');

$this->section('admin-search-box-addons');
?>
<p>
    <a class="btn btn-cyan" href="/admin/categories/<?= $this->tab ?>/add"><i class="fa fa-plus"></i> <?= $this->text("admin-{$this->tab}-add") ?></a>
</p>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

    <?= $this->insert('admin/categories/partials/tabs') ?>

    <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, $this->fields)]) ?>

  </div>
</div>

<?php $this->replace() ?>


