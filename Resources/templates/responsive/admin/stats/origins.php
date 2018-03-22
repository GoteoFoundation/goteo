<?php

$this->layout('admin/stats/layout');

$query = http_build_query($this->filters);

?>

<?php $this->section('admin-container-body') ?>

<?= $this->supply('admin-stats-filters', $this->insert('admin/stats/partials/filters')) ?>

<div class="panel">
  <div class="panel-body">

    <div class="row">
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-project-referers') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/referers/project?<?= $filters ?>"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-invests-referers') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/referers/invests?<?= $filters ?>"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-call-referers') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/referers/call?<?= $filters ?>"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-project-devices') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/devices/project?group_by=category&<?= $filters ?>"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-invests-devices') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/devices/invests?group_by=category&<?= $filters ?>"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-call-devices') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/devices/call?group_by=category&<?= $filters ?>"></div>
        </div>
    </div>
  </div>
</div>


<?php $this->replace() ?>
