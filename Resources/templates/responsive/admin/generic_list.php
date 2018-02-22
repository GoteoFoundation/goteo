<?php

$this->layout('admin/layout');

$this->section('admin-content');

$link_prefix = $this->link_prefix ? '/admin' . $this->link_prefix : '';
?>
<div class="admin-content">
  <div class="inner-container">
    <h2><?= $this->text('admin-list') ?></h2>

    <h5><?= $this->text('admin-list-total', $this->total) ?></h5>

    <ul class="generic-list">
    <?php foreach($this->model_list_entries($this->list) as $entry): ?>
        <li>
        <?php
        $t = count($entry);
        $i = 1;
        foreach($entry as $key => $val) {
            $vars = ['value' => $val, 'last' => false, 'link' => '', 'class' => ''];
            if($link_prefix && $entry['id']) $vars['link'] = $link_prefix . $entry['id'];
            if($i === $t) $vars['last'] = true;
            echo $this->insertIf("admin/partials/objects/$key", $vars) ?: $this->insert("admin/partials/objects/text", $vars);
            $i++;
        }
        ?>
        </li>
    <?php endforeach ?>
    </ul>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>

  </div>
</div>

<?php $this->replace() ?>

