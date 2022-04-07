<?php

$this->layout('admin/faq/layout');

$this->section('admin-search-box-addons');

if ($this->current_subsection)
    $keys = ['id', 'title', 'order', 'subsection', 'actions'];
else
    $keys = ['id', 'title', 'subsection', 'actions'];
?>

<label for="sections-filter"><?= $this->t('admin-faq-subsections') ?></label>
<div class="form form-group">
    <select id="sections-filter" name="sections-list" class="form-control" style="margin-bottom:1em;" onchange="window.location.href='/admin/faq/' + this.value">
        <?php if (!$this->current_subsection) : ?>
            <option selected="selected"><?= $this->t('admin-faq-subsections-all')?></option>
        <?php endif; ?>
        <?php foreach ($this->faq_subsections as $section => $subsections) : ?>
            <optgroup label="<?= $section ?>">
                <?php foreach($subsections as $name => $id): ?>
                    <option value="<?= $name; ?>" <?= ($id == $this->current_subsection)? 'selected="selected"': '' ?>><?= $name; ?></option>
                <?php endforeach; ?>
            </optgroup>
        <?php endforeach; ?>
    </select>
</div>

<a class="btn btn-cyan" href="/admin/faq/add"><i class="fa fa-plus"></i> <?= $this->text('admin-faq-add') ?></a>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

  <h5><?= $this->text('admin-list-total', $this->total) ?></h5>
  <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, $keys)]) ?>

<?php $this->replace() ?>
