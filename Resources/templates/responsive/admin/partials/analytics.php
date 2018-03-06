<div class="panel">
  <div class="panel-body">

    <div class="row">
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-project-referers') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/referers/project"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-invests-referers') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/referers/invests"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-call-referers') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/referers/call"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-project-devices') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/devices/project?group_by=category"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-invests-devices') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/devices/invests?group_by=category"></div>
        </div>
        <div class="chart-wrapper col-sm-4 col-xs-12">
            <h5><?= $this->text('admin-all-call-devices') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/devices/call?group_by=category"></div>
        </div>
    </div>
  </div>
</div>
