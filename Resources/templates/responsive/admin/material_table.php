<?php

$this->layout('admin/layout');

$this->section('admin-content');

$link_prefix = $this->link_prefix ? '/admin' . $this->link_prefix : '';

// Inspired in https://codepen.io/zavoloklom/pen/IGkDzT

$list = $this->model_list_entries($this->list);
?>
<div class="admin-content">
  <div class="inner-container">
    <h2><?= $this->text('admin-list') ?></h2>

    <?= $this->insert('admin/partials/search_box') ?>

    <h5><?= $this->text('admin-list-total', $this->total) ?></h5>

    <?php if($list):
        $first = current($list);
    ?>
      <div class="table-responsive-vertical shadow-z-1">
      <table class="material-table table-hover model-<?= $first->getModelName() ?>">
        <thead><tr>
        <?php foreach($first->getDefaultKeys() as $key):?>
            <th><?= $first->getLabel($key) ?></th>
        <?php endforeach ?>
        </tr></thead>
        <tbody>
        <?php foreach($list as $entry): ?>
            <tr id="tr-<?= $entry->getId() ?>">
            <?php
            $t = count($entry);
            foreach($entry as $key => $val):
                $vars = ['value' => $val, 'ob' => $entry];
            ?>
                <td data-title="<?= $entry->getLabel($key) ?>"><?= $this->insertIf("admin/partials/objects/$key", $vars) ?: $this->insert("admin/partials/objects/text", $vars) ?></td>
            <?php endforeach ?>
            </tr>
        <?php endforeach ?>
        </tbody>
      </table>
      </div>

      <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10, 'a_extra' => 'class="pronto"']) ?>

    <?php else: ?>
        <p class="alert alert-info"><?= $this->text('admin-empty-list') ?></p>
    <?php endif ?>
  </div>
</div>

<?php $this->replace() ?>

