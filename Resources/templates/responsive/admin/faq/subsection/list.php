<?php

$this->layout('admin/faq/subsection/layout');

$this->section('admin-search-box-addons');

$keys = [
    'id',
    'name',
    'subsection',
    'order',
    'actions'
];
?>

<label for="sections-filter"><?= $this->t('admin-faq-sections') ?></label>
<div class="form form-group">
    <select id="sections-filter" name="sections-list" class="form-control" style="margin-bottom:1em;" onchange="window.location.href='/admin/faqsubsection/section/' + this.value">
        <?php if (!$this->current_section) : ?>
            <option selected="selected"><?= $this->t('admin-faq-sections-all')?></option>
        <?php endif; ?>
        <?php foreach ($this->faq_sections as $section) : ?>
            <option value="<?= $section->id ?>" <?= ($section->id == $this->current_section)? 'selected="selected"': '' ?>><?= $section->name; ?></option>
        <?php endforeach; ?>
    </select>
</div>

<a class="btn btn-cyan" href="/admin/faqsubsection/add"><i class="fa fa-plus"></i> <?= $this->text('admin-faq-subsections-add') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

  <h5><?= $this->text('admin-list-total', $this->total) ?></h5>
  <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, $keys)]) ?>

<?php $this->replace() ?>
