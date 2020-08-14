<?php

$this->layout('admin/faq/layout');

$this->section('admin-search-box-addons');

?>

<div>
    <select id="sections-filter" name="sections-list" class="form-control" style="margin-bottom:1em;" onchange="goChannelPromote()">
        <?php if (!$this->selectedNode) : ?>
        <option selected="selected" hidden></option>
        <?php endif; ?>
        <?php foreach ($this->sections as $nodeId => $nodeName) : ?>
        <option value="<?php echo $nodeId; ?>" <?php if ($nodeId == $this->selectedNode) echo 'selected="selected"'; ?>><?php echo $nodeName; ?></option>
        <?php endforeach; ?>
    </select>
</div>

<a class="btn btn-cyan" href="/add"><i class="fa fa-plus"></i> <?= $this->text('admin-faqs-add') ?></a>

<?php $this->replace() ?>


<?php $this->section('admin-container-body') ?>

  <h5><?= $this->text('admin-list-total', $this->total) ?></h5>

  <?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['id', 'title', 'order', 'section', 'actions'])]) ?>

  </div>
</div>

<?php $this->replace() ?>