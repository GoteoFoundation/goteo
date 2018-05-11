<div class="btn-group" role="group">
    <button type="button" class="btn btn-default active d3-chart-updater" data-target="#invests-metrics" data-field="total" data-title="<?= $this->text('admin-aggregate-invests') ?>" data-description="<?= $this->text('admin-aggregate-invests-desc') ?>"><?= $this->text('admin-aggregate-invests') ?></button>
    <button type="button" class="btn btn-default d3-chart-updater" data-target="#invests-metrics" data-field="average" data-title="<?= $this->text('admin-aggregate-invests-average') ?>" data-description="<?= $this->text('admin-aggregate-invests-average-desc') ?>"><?= $this->text('admin-aggregate-invests-average') ?></button>
</div>

<span class="pull-right"><?= $this->insert('dashboard/project/partials/boolean', ['name' => 'autoupdate', 'value' => false, 'input_class' => 'd3-chart-updater', 'input_data' => ['target' => '#invests-metrics', 'interval' => 15]]) ?> <?= $this->text('admin-aggregate-autoupdate') ?></span>

<div id="invests-metrics" class="d3-chart loading time-metrics spacer-20" data-source="/api/charts/aggregates/invests?<?= $this->query ?>" data-field="total" data-title="<?= $this->text('admin-aggregate-invests') ?>" data-description="<?= $this->text('admin-aggregate-invests-desc') ?>" data-format="<?= $this->text('admin-aggretate-invests-format', ['%CURRENCY%' => $this->get_currency()]) ?>"></div>

