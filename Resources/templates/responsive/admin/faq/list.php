<?php

$this->layout('admin/faq/layout');

$this->section('admin-search-box-addons');

?>

<div>
    <select id="sections-filter" name="sections-list" class="form-control" style="margin-bottom:1em;" onchange="window.location.href='/admin/faq/' + this.value">
        <?php if (!$this->current_subsection) : ?>
        <option selected="selected">Todas las subsecciones</option>
        <?php endif; ?>
        <?php foreach ($this->faq_subsections as $subsection) : ?>
        <option value="<?php echo $subsection->id; ?>" <?php if ($subsection->id == $this->current_subsection) echo 'selected="selected"'; ?>><?php echo $subsection->name; ?></option>
        <?php endforeach; ?>
    </select>
</div>

<a class="btn btn-cyan" href="/admin/faq/add"><i class="fa fa-plus"></i> <?= $this->text('admin-faq-add') ?></a>

<?php $this->replace() ?>


<?php $this->section('admin-container-body') ?>

  <h5><?= $this->text('admin-list-total', $this->total) ?></h5>

  <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['id', 'title', 'order', 'subsection', 'actions'])]) ?>

  </div>
</div>

<?php $this->replace() ?>