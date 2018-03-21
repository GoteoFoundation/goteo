<?php

$this->layout('admin/stats/layout');

$query = http_build_query($this->filters);

?>

<?php $this->section('admin-container-body') ?>

<?= $this->supply('admin-stats-filters', $this->insert('admin/stats/partials/filters')) ?>


<div class="panel">
  <div class="panel-body">
    <h5><?= $this->text('admin-stats-project-totals') ?></h5>

    <div class="d3-chart discrete-values" data-source="/api/charts/totals/projects" data-interval="15" data-flash-time="30">
        <ul class="row list-unstyled">
            <li class="col-xs-2 col-xxs-4" data-property="created.today" data-title="<?= $this->text('admin-projects-created-today') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="published.today" data-title="<?= $this->text('admin-projects-published-today') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="published.yesterday" data-title="<?= $this->text('admin-projects-published-yesterday') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="reviewing.week" data-title="<?= $this->text('admin-projects-review-week') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="rejected.week" data-title="<?= $this->text('admin-projects-rejected-week') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="published.year" data-title="<?= $this->text('admin-projects-published-year') ?>"></li>
        </ul>

    </div>

    <h5><?= $this->text('admin-stats-invest-totals') ?></h5>

    <div class="d3-chart discrete-values" data-source="/api/charts/totals/invests" data-interval="15" data-flash-time="15">
        <ul class="row list-unstyled">
            <li class="col-xs-2 col-xxs-4" data-property="global.raised.today.amount" data-title="<?= $this->text('admin-invest-raised-amount-today') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="global.raised.yesterday.amount" data-title="<?= $this->text('admin-invest-raised-amount-yesterday') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="global.commissions.today" data-title="<?= $this->text('admin-invest-commissions-today') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="global.commissions.yesterday" data-title="<?= $this->text('admin-invest-commissions-yesterday') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="fees.month" data-title="<?= $this->text('admin-invest-fees-month') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="fees.year" data-title="<?= $this->text('admin-invest-fees-year') ?>"></li>
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
