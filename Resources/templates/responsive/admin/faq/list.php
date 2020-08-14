<?php

$this->layout('admin/faq/layout');

$this->section('admin-search-box-addons');

?>

<div>
    <select id="sections-filter" name="sections-list" class="form-control" style="margin-bottom:1em;" onchange="window.location.href='/admin/faq/' + this.value">
        <?php if (!$this->current_section) : ?>
        <option selected="selected" hidden></option>
        <?php endif; ?>
        <?php foreach ($this->faq_sections as $section => $section_name) : ?>
        <option value="<?php echo $section; ?>" <?php if ($section == $this->current_section) echo 'selected="selected"'; ?>><?php echo $section_name; ?></option>
        <?php endforeach; ?>
    </select>
</div>

<a class="btn btn-cyan" href="/admin/faq/add"><i class="fa fa-plus"></i> <?= $this->text('admin-faq-add') ?></a>

<?php $this->replace() ?>


<?php $this->section('admin-container-body') ?>

  <h5><?= $this->text('admin-list-total', $this->total) ?></h5>

  <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['id', 'title', 'order', 'section', 'actions'])]) ?>

  </div>
</div>

<?php $this->replace() ?>