<?php

$this->layout('admin/stats/layout');

// $query = http_build_query($this->filters);
$query = '';

?>

<?php $this->section('admin-container-body') ?>

<h5><?= $this->text('admin-stats-project-totals') ?></h5>

<div class="panel">
  <div class="panel-body">
    <?= $this->insert('admin/stats/totals/partials/menu') ?>


    <?= $this->insert('admin/stats/totals/partials/projects', ['query' => $query]) ?>

  </div>
</div>

<?php $this->replace() ?>

