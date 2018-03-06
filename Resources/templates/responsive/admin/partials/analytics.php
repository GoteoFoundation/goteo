<div class="panel">
  <div class="panel-body">

    <div class="row">
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <h5><?= $this->text('admin-origin-project-referer') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/referers/project"></div>
        </div>
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <h5><?= $this->text('admin-origin-call-referer') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/referers/call"></div>
        </div>
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <h5><?= $this->text('admin-origin-invests-referer') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/referers/invests"></div>
        </div>
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <h5><?= $this->text('admin-origin-project-device') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/devices/project?group_by=category"></div>
        </div>
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <h5><?= $this->text('admin-origin-invests-device') ?></h5>
            <div class="d3-chart percent-pie auto-enlarge" data-source="/api/charts/devices/invests?group_by=category"></div>
        </div>
    </div>
  </div>
</div>
