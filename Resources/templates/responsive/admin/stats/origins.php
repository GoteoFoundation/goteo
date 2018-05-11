<?php

$this->layout('admin/stats/layout');

$query = http_build_query($this->filters);
$defaults = [];
foreach($this->a('engines') as $q) {
    if($this->has_query($q)) $defaults[] = $q;
}

?>

<?php $this->section('admin-stats-head') ?>

    <?= $this->insert('admin/partials/typeahead') ?>
    <?= $this->supply('admin-stats-filters', $this->insert('admin/stats/partials/filters', ['hidden' => $this->filters + ['text' => $this->text]])) ?>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

<div class="panel">
  <div class="panel-body">

    <?php foreach($defaults as $engine): ?>
        <p><?= $this->text("admin-stats-projects-$engine-desc") ?></p>
    <?php endforeach ?>

    <div class="row">
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-project-referers') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/referers/project?<?= $query ?>"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-invests-referers') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/referers/invests?<?= $query ?>"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-call-referers') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/referers/call?<?= $query ?>"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-project-devices') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/devices/project?group_by=category&<?= $query ?>"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-invests-devices') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/devices/invests?group_by=category&<?= $query ?>"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-call-devices') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/charts/devices/call?group_by=category&<?= $query ?>"></div>
        </div>
    </div>
  </div>
</div>


<?php $this->replace() ?>
