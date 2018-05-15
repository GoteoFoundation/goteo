<?php

$this->layout('admin/stats/layout');

$query = http_build_query($this->filters);

?>

<?php $this->section('admin-container-body') ?>

<?= $this->supply('admin-stats-filters', $this->insert('admin/stats/partials/filters')) ?>


<div class="panel">
  <div class="panel-body">
    <h5><?= $this->text('admin-stats-project-totals') ?></h5>

    <div class="d3-chart loading discrete-values" data-source="/api/charts/totals/projects" data-interval="40" data-flash-time="30">
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

    <div class="row">
        <ul class="list-unstyled d3-chart loading discrete-values" data-source="/api/charts/totals/invests/raised/global/today,week" data-interval="40" data-interval-delay="10" data-flash-time="30">
            <li class="col-xs-2 col-xxs-4" data-property="raised.global.today.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-today') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="raised.global.yesterday.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-yesterday') ?>"></li>
            <!-- <li class="col-xs-2 col-xxs-4" data-property="raised.global.today.amount_diff_formatted" data-title="<?= $this->text('admin-invest-diff-yesterday') ?>"></li> -->
            <li class="col-xs-2 col-xxs-4" data-property="raised.global.today.amount_gain_formatted" data-title="<?= $this->text('admin-invest-diff-yesterday') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="raised.global.week.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-week') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="raised.global.last_week.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-last_week') ?>"></li>
            <!-- <li class="col-xs-2 col-xxs-4" data-property="raised.global.week.amount_diff_formatted" data-title="<?= $this->text('admin-invest-diff-last_week') ?>"></li> -->
            <li class="col-xs-2 col-xxs-4" data-property="raised.global.week.amount_gain_formatted" data-title="<?= $this->text('admin-invest-diff-last_week') ?>"></li>
        </ul>
        <ul class="list-unstyled d3-chart loading discrete-values" data-source="/api/charts/totals/invests/commissions/global/month" data-interval="40" data-interval-delay="20" data-flash-time="30">
            <li class="col-xs-2 col-xxs-4" data-property="commissions.global.month.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-month') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="commissions.global.month.lost_formatted" data-title="<?= $this->text('admin-invest-commissions-lost-month') ?>"></li>
        </ul>
        <ul class="list-unstyled d3-chart loading discrete-values" data-source="/api/charts/totals/invests/fees/global/month,year" data-interval="40" data-interval-delay="30" data-flash-time="30">
            <li class="col-xs-2 col-xxs-4" data-property="fees.global.month.total_formatted" data-title="<?= $this->text('admin-invest-fees-month') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="fees.global.month.total_gain_formatted" data-title="<?= $this->text('admin-invest-diff-last_month') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="fees.global.year.total_formatted" data-title="<?= $this->text('admin-invest-fees-year') ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="fees.global.year.total_gain_formatted" data-title="<?= $this->text('admin-invest-diff-last_year') ?>"></li>
        </ul>

    </div>

    <p><a class="pronto" href="/admin/stats/totals/projects"><i class="fa fa-search"></i> <?= $this->text('regular-see_more') ?></a></p>

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
                <div class="d3-chart loading percent-pie" data-source="/api/charts/referers/project?<?= $query ?>"></div>
            </a>
        </div>

        <div class="chart-wrapper col-sm-6 col-xs-12">
            <a class="pronto" href="/admin/stats/origins">
                <h5><?= $this->text('admin-all-project-devices') ?></h5>
                <div class="d3-chart loading percent-pie" data-source="/api/charts/devices/project?group_by=category&<?= $query ?>"></div>
            </a>
        </div>


    </div>
    <p><a class="pronto" href="/admin/stats/origins?<?= $query ?>"><i class="fa fa-pie-chart"></i> <?= $this->text('regular-see_more') ?></a></p>

  </div>
</div>

<?php $this->replace() ?>
