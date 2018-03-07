<?php

$this->layout('admin/stats/layout');

$query = http_build_query($this->filters);

?>

<?php $this->section('admin-stats-container') ?>
<div class="panel">
  <div class="panel-body">
    <h5><?= $this->text('admin-aggregate-timeline') ?></h5>

    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default d3-chart-updater" data-target="#invests-metrics" data-field="total" data-title="<?= $this->text('admin-aggregate-invests') ?>" data-description="<?= $this->text('admin-aggregate-invests-desc') ?>"><?= $this->text('admin-aggregate-invests') ?></button>
      <button type="button" class="btn btn-default d3-chart-updater" data-target="#invests-metrics" data-field="average" data-title="<?= $this->text('admin-aggregate-invests-average') ?>" data-description="<?= $this->text('admin-aggregate-invests-average-desc') ?>"><?= $this->text('admin-aggregate-invests-average') ?></button>
    </div>

    <div id="invests-metrics" class="d3-chart time-metrics" data-source="/api/charts/aggregates/invests?<?= $query ?>" data-field="total" data-title="<?= $this->text('admin-aggregate-invests') ?>" data-description="<?= $this->text('admin-aggregate-invests-desc') ?>" data-format="<?= $this->text('admin-aggretate-invests-format', ['%CURRENCY%' => $this->get_currency()]) ?>"></div>


    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default d3-chart-updater" data-target="#projects-metrics" data-field="total" data-title="<?= $this->text('admin-aggregate-projects') ?>" data-description="<?= $this->text('admin-aggregate-projects-desc') ?>"><?= $this->text('admin-aggregate-projects') ?></button>
      <button type="button" class="btn btn-default d3-chart-updater" data-target="#projects-metrics" data-field="average" data-title="<?= $this->text('admin-aggregate-projects-average') ?>" data-description="<?= $this->text('admin-aggregate-projects-average-desc') ?>"><?= $this->text('admin-aggregate-projects-average') ?></button>
    </div>

    <div id="projects-metrics" class="d3-chart time-metrics" data-source="/api/charts/aggregates/projects?<?= $query ?>" data-field="total" data-title="<?= $this->text('admin-aggregate-projects') ?>" data-description="<?= $this->text('admin-aggregate-projects-desc') ?>" data-format="<?= $this->text('admin-aggretate-projects-format', ['%CURRENCY%' => $this->get_currency()]) ?>"></div>projects
  </div>
</div>

<?php $this->replace() ?>
