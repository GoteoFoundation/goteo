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

    <?php if($list): ?>
      <div class="table-responsive-vertical shadow-z-1">
      <table class="material-table table-hover table-mc-cyan">
        <thead><tr>
        <?php
        $entry = current($list);
        foreach($entry as $key => $val):
            // Skip avatar (shown on name object)
            if($key === 'avatar' && isset($entry['name'])) continue;
        ?>
            <th><?= $this->text("admin-title-$key") ?></th>
        <?php endforeach ?>
        </tr></thead>
        <tbody>
        <?php foreach($list as $entry): ?>
            <tr>
            <?php
            $t = count($entry);
            foreach($entry as $key => $val):
                $vars = ['value' => $val, 'last' => false, 'link' => '', 'class' => '', 'entry' => $entry];
                if($link_prefix && $entry['id']) $vars['link'] = $link_prefix . $entry['id'];
                // Skip avatar (shown on name object)
                if($key === 'avatar' && isset($entry['name'])) continue;
            ?>
                <td data-title="<?= $this->text("admin-title-$key") ?>"><?= $this->insertIf("admin/partials/objects/$key", $vars) ?: $this->insert("admin/partials/objects/text", $vars) ?></td>
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

