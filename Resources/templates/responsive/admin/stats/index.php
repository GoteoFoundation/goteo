<?php

$this->layout('admin/stats/layout');

?>

<?php $this->section('admin-stats-container') ?>
<div class="panel">
  <div class="panel-body">
    <h5><?= $this->text('admin-aggregate-invests') ?></h5>

  </div>
</div>
<div class="panel">
  <div class="panel-body">

    <div class="row">
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <a class="pronto" href="/admin/stats/origins">
                <h5><?= $this->text('admin-all-project-referers') ?></h5>
                <div class="d3-chart percent-pie" data-source="/api/charts/referers/project?<?= $this->get_querystring() ?>"></div>
            </a>
        </div>

        <div class="chart-wrapper col-sm-6 col-xs-12">
            <a class="pronto" href="/admin/stats/origins">
                <h5><?= $this->text('admin-all-project-devices') ?></h5>
                <div class="d3-chart percent-pie" data-source="/api/charts/devices/project?group_by=category&<?= $this->get_querystring() ?>"></div>
            </a>
        </div>


    </div>
    <p><a class="pronto" href="/admin/stats/origins"><i class="fa fa-pie-chart"></i> <?= $this->text('regular-see_more') ?></a></p>

  </div>
</div>

<?php $this->replace() ?>
