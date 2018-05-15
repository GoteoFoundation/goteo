<div class="panel">
  <div class="panel-body">

    <div class="row">
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <h5><?= $this->text('dashboard-origin-project-referer') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/projects/<?= $this->project->id ?>/charts/referer/project"></div>
        </div>
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <h5><?= $this->text('dashboard-origin-invests-referer') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/projects/<?= $this->project->id ?>/charts/referer/invests"></div>
        </div>
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <h5><?= $this->text('dashboard-origin-project-device') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/projects/<?= $this->project->id ?>/charts/device/project?group_by=category"></div>
        </div>
        <div class="chart-wrapper col-sm-6 col-xs-12">
            <h5><?= $this->text('dashboard-origin-invests-device') ?></h5>
            <div class="d3-chart loading percent-pie auto-enlarge" data-source="/api/projects/<?= $this->project->id ?>/charts/device/invests?group_by=category"></div>
        </div>
    </div>

    <?= $this->markdown($this->text('dashboard-origin-disclaimer')) ?>
  </div>
</div>
