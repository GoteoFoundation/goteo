<?php

$this->layout('admin/stats/layout');

$query = http_build_query($this->filters);

?>

<?php $this->section('admin-stats-container') ?>
<div class="panel">
  <div class="panel-body">
    <h5><?= $this->text('admin-aggregate-timeline') ?></h5>

    <div class="d3-chart time-metrics" data-source="/api/charts/aggregates/invests?<?= $query ?>" data-field="total" data-title="<?= $this->text('admin-aggregate-invests') ?>" data-description="<?= $this->text('admin-aggregate-invests-desc') ?>" data-format="<?= $this->text('admin-aggretate-invests-format', ['%CURRENCY%' => $this->get_currency()]) ?>"></div>

    <p><a class="pronto" href="/admin/stats/timeline"><i class="fa fa-line-chart"></i> <?= $this->text('regular-see_more') ?></a></p>

  </div>
</div>
<div class="panel">
  <div class="panel-body">

    <div class="row">
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <a class="pronto" href="/admin/stats/origins">
                <h5><?= $this->text('admin-all-project-referers') ?></h5>
                <div class="d3-chart percent-pie" data-source="/api/charts/referers/project?<?= $query ?>"></div>
            </a>
        </div>

        <div class="chart-wrapper col-sm-6 col-xs-12">
            <a class="pronto" href="/admin/stats/origins">
                <h5><?= $this->text('admin-all-project-devices') ?></h5>
                <div class="d3-chart percent-pie" data-source="/api/charts/devices/project?group_by=category&<?= $query ?>"></div>
            </a>
        </div>


    </div>
    <p><a class="pronto" href="/admin/stats/origins"><i class="fa fa-pie-chart"></i> <?= $this->text('regular-see_more') ?></a></p>

  </div>
</div>

<?php $this->replace() ?>
