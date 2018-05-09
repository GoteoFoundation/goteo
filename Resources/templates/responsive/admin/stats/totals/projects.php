<?php

$this->layout('admin/stats/layout');

$filters = $this->a('filters');

?>

<?php $this->section('admin-stats-head') ?>

    <?= $this->insert('admin/partials/typeahead', ['value' => $value, 'engines' => ['channel', 'call', 'matcher', 'consultant']]) ?>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

<div class="panel">
  <div class="panel-body">

    <h5><?= $this->text('admin-stats-project-totals') ?></h5>

    <?= $this->insert('admin/stats/totals/partials/projects', ['query' => http_build_query($filters, '', '&')]) ?>

  </div>
</div>

<?php $this->replace() ?>

