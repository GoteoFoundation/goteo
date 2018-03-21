<?php

$this->layout('admin/stats/layout');

$query = http_build_query($this->filters);

?>

<?php $this->section('admin-container-body') ?>

<?= $this->supply('admin-stats-filters', $this->insert('admin/stats/partials/filters')) ?>

<div class="panel">
  <div class="panel-body">
    <h5><?= $this->text('admin-aggregate-timeline') ?></h5>

    <?= $this->insert('admin/stats/partials/timeline/invests', ['query' => $query]) ?>

    <?= $this->insert('admin/stats/partials/timeline/projects', ['query' => $query]) ?>


  </div>
</div>

<?php $this->replace() ?>

