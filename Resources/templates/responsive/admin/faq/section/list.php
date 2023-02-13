<?php

$this->layout('admin/faq/section/layout');

$this->section('admin-search-box-addons');

$keys = [
    'id',
    'name',
    'slug',
    'actions'
]
?>

<h2><?= $this->t('admin-faq-sections') ?></h2>

<a class="btn btn-cyan" href="/admin/faqsection/add"><i class="fa fa-plus"></i> <?= $this->text('admin-faq-sections-add') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

  <h5><?= $this->text('admin-list-total', $this->total) ?></h5>
  <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, $keys)]) ?>

<?php $this->replace() ?>
