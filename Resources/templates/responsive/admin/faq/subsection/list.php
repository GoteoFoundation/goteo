<?php

$this->layout('admin/faq/subsection/layout');

$this->section('admin-search-box-addons');

$keys = [
    'id',
    'name',
    'subsection',
    'order',
    'actions'
]
?>

<h2><?= $this->t('admin-faq-sections') ?></h2>

<a class="btn btn-cyan" href="/admin/faqsubsection/add"><i class="fa fa-plus"></i> <?= $this->text('admin-faq-subsections-add') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

  <h5><?= $this->text('admin-list-total', $this->total) ?></h5>
  <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, $keys)]) ?>

<?php $this->replace() ?>
