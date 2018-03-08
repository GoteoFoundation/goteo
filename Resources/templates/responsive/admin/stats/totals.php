<?php

$this->layout('admin/stats/layout');

// $query = http_build_query($this->filters);
$query = '';

?>

<?php $this->section('admin-container-body') ?>

<div class="panel">
  <div class="panel-body">
    <h5><?= $this->text('admin-stats-project-totals') ?></h5>

    <?= $this->insert('admin/stats/partials/totals/projects', ['query' => $query]) ?>

    <?= $this->insert('admin/stats/partials/totals/invests', ['query' => $query]) ?>

  </div>
</div>

<?php $this->replace() ?>

