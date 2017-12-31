<div class="panel">
  <div class="panel-body">

    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <div class="d3-chart percent-pie" data-source="/api/charts/<?= $this->project->id ?>/referer/project"></div>
            <h5><?= $this->text('dashboard-origin-project-referer') ?></h5>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="d3-chart percent-pie" data-source="/api/charts/<?= $this->project->id ?>/referer/invests"></div>
            <h5><?= $this->text('dashboard-origin-invests-referer') ?></h5>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="d3-chart percent-pie" data-source="/api/charts/<?= $this->project->id ?>/device/project?group_by=category"></div>
            <h5><?= $this->text('dashboard-origin-project-device') ?></h5>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="d3-chart percent-pie" data-source="/api/charts/<?= $this->project->id ?>/device/invests?group_by=category"></div>
            <h5><?= $this->text('dashboard-origin-invests-device') ?></h5>
        </div>
    </div>

    <?= $this->markdown($this->text('dashboard-origin-disclaimer')) ?>
  </div>
</div>
