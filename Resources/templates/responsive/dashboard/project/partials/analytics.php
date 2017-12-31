<div class="panel">
  <div class="panel-body">

    <div class="row">
        <div class="col-sm-3 col-xs-6">
            <div class="d3-chart percent-pie" data-source="/api/charts/<?= $this->project->id ?>/referer/project"></div>
            <h5>Project referer origins</h5>
        </div>
        <div class="col-sm-3 col-xs-6">
            <div class="d3-chart percent-pie" data-source="/api/charts/<?= $this->project->id ?>/referer/invests"></div>
            <h5>Project invests origins</h5>
        </div>
        <div class="col-sm-3 col-xs-6">
            <div class="d3-chart percent-pie" data-source="/api/charts/<?= $this->project->id ?>/device/project?group_by=category"></div>
            <h5>Project devices origins</h5>
        </div>
        <div class="col-sm-3 col-xs-6">
            <div class="d3-chart percent-pie" data-source="/api/charts/<?= $this->project->id ?>/device/invests?group_by=category"></div>
            <h5>Project invests devices origins</h5>
        </div>
    </div>
  </div>
</div>
