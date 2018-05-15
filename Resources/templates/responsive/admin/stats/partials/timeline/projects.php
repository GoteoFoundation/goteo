<div class="btn-group" role="group">
    <button type="button" class="btn btn-default active d3-chart-updater" data-target="#projects-metrics" data-field="total" data-title="<?= $this->text('admin-aggregate-projects') ?>" data-description="<?= $this->text('admin-aggregate-projects-desc') ?>"><?= $this->text('admin-aggregate-projects') ?></button>
    <button type="button" class="btn btn-default d3-chart-updater" data-target="#projects-metrics" data-field="average" data-title="<?= $this->text('admin-aggregate-projects-average') ?>" data-description="<?= $this->text('admin-aggregate-projects-average-desc') ?>"><?= $this->text('admin-aggregate-projects-average') ?></button>
</div>

<span class="pull-right"><?= $this->insert('dashboard/project/partials/boolean', ['name' => 'autoupdate', 'value' => false, 'input_class' => 'd3-chart-updater', 'input_data' => ['target' => '#projects-metrics', 'interval' => 15]]) ?> <?= $this->text('admin-aggregate-autoupdate') ?></span>

<div id="projects-metrics" class="d3-chart loading time-metrics spacer-20" data-source="/api/charts/aggregates/projects?<?= $this->query ?>" data-field="total" data-title="<?= $this->text('admin-aggregate-projects') ?>" data-description="<?= $this->text('admin-aggregate-projects-desc') ?>" data-format="<?= $this->text('admin-aggretate-projects-format', ['%CURRENCY%' => $this->get_currency()]) ?>"></div>
