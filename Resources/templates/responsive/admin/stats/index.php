<?php

$this->layout('admin/stats/layout');

$query = http_build_query($this->filters);

?>

<?php $this->section('admin-stats-container') ?>
<div class="panel">
  <div class="panel-body">
    <h5><?= $this->text('admin-projects-stats') ?></h5>

    <div class="d3-chart discrete-values" data-source="/api/charts/totals/projects?<?= $this->query ?>" data-title="<?= $this->text('admin-projects-stats') ?>" data-interval="15">
        <ul class="row list-unstyled">
            <li class="col-xs-4 col-sm-2" data-property="created.today" data-title="<?= $this->text('admin-projects-created-today') ?>"></li>
            <li class="col-xs-4 col-sm-2" data-property="published.today" data-title="<?= $this->text('admin-projects-published-today') ?>"></li>
            <li class="col-xs-4 col-sm-2" data-property="published.yesterday" data-title="<?= $this->text('admin-projects-published-yesterday') ?>"></li>
            <li class="col-xs-4 col-sm-2" data-property="reviewing.week" data-title="<?= $this->text('admin-projects-review-week') ?>"></li>
            <li class="col-xs-4 col-sm-2" data-property="rejected.week" data-title="<?= $this->text('admin-projects-created-week') ?>"></li>
            <li class="col-xs-4 col-sm-2" data-property="published.year" data-title="<?= $this->text('admin-projects-published-year') ?>"></li>
            <li class="col-xs-4 col-sm-2" data-property="published.lastyear" data-title="<?= $this->text('admin-projects-published-lastyear') ?>"></li>
        </ul>

    </div>

    <p><a class="pronto" href="/admin/stats/totals"><i class="fa fa-paragraph"></i> <?= $this->text('regular-see_more') ?></a></p>

  </div>
</div>

<div class="panel">
  <div class="panel-body">
    <h5><?= $this->text('admin-aggregate-timeline') ?></h5>

    <?= $this->insert('admin/stats/partials/timeline/invests', ['query' => $query]) ?>

    <p><a class="pronto" href="/admin/stats/timeline?<?= $query ?>"><i class="fa fa-line-chart"></i> <?= $this->text('regular-see_more') ?></a></p>

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
    <p><a class="pronto" href="/admin/stats/origins?<?= $query ?>"><i class="fa fa-pie-chart"></i> <?= $this->text('regular-see_more') ?></a></p>

  </div>
</div>

<?php $this->replace() ?>
