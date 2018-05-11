<?php

$this->layout('admin/stats/layout');

$filters = $this->a('filters');

$engines = ['channel', 'call', 'matcher', 'consultant'];
$defaults = [];
foreach($engines as $q) {
    if($this->has_query($q)) $defaults[] = $q;
}

?>

<?php $this->section('admin-stats-head') ?>

    <?= $this->insert('admin/partials/typeahead', ['engines' => $engines, 'defaults' => $engines]) ?>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

<div class="panel">
  <div class="panel-body">

    <h4><?= $this->text('admin-stats-project-totals') ?></h4>

    <?php foreach($defaults as $engine): ?>
        <p><?= $this->text("admin-stats-projects-$engine-desc") ?></p>
    <?php endforeach ?>

    <?= $this->insert('admin/stats/totals/partials/projects', ['query' => http_build_query($filters, '', '&')]) ?>

  </div>
</div>

<?php $this->replace() ?>

