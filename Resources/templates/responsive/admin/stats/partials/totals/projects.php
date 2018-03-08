<div class="d3-chart discrete-values" data-source="/api/charts/totals/projects?<?= $this->query ?>" data-title="<?= $this->text('admin-projects-stats') ?>" data-interval="15">
    <ul class="row list-unstyled">
        <li class="col-xs-4 col-sm-2" data-property="created.today" data-title="<?= $this->text('admin-projects-created-today') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="created.yesterday" data-title="<?= $this->text('admin-projects-created-yesterday') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="created.week" data-title="<?= $this->text('admin-projects-created-week') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="created.month" data-title="<?= $this->text('admin-projects-created-month') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="created.year" data-title="<?= $this->text('admin-projects-created-year') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="created.lastyear" data-title="<?= $this->text('admin-projects-created-lastyear') ?>"></li>
    </ul>

    <ul class="row list-unstyled">
        <li class="col-xs-4 col-sm-2" data-property="published.today" data-title="<?= $this->text('admin-projects-published-today') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="published.yesterday" data-title="<?= $this->text('admin-projects-published-yesterday') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="published.week" data-title="<?= $this->text('admin-projects-published-week') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="published.month" data-title="<?= $this->text('admin-projects-published-month') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="published.year" data-title="<?= $this->text('admin-projects-published-year') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="published.lastyear" data-title="<?= $this->text('admin-projects-published-lastyear') ?>"></li>
    </ul>

    <ul class="row list-unstyled">
        <li class="col-xs-4 col-sm-2" data-property="reviewing.today" data-title="<?= $this->text('admin-projects-review-today') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="reviewing.yesterday" data-title="<?= $this->text('admin-projects-review-yesterday') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="reviewing.week" data-title="<?= $this->text('admin-projects-review-week') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="reviewing.month" data-title="<?= $this->text('admin-projects-review-month') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="reviewing.year" data-title="<?= $this->text('admin-projects-review-year') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="reviewing.lastyear" data-title="<?= $this->text('admin-projects-review-lastyear') ?>"></li>
    </ul>

    <ul class="row list-unstyled">
        <li class="col-xs-4 col-sm-2" data-property="rejected.today" data-title="<?= $this->text('admin-projects-rejected-today') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="rejected.yesterday" data-title="<?= $this->text('admin-projects-rejected-yesterday') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="rejected.week" data-title="<?= $this->text('admin-projects-rejected-week') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="rejected.month" data-title="<?= $this->text('admin-projects-rejected-month') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="rejected.year" data-title="<?= $this->text('admin-projects-rejected-year') ?>"></li>
        <li class="col-xs-4 col-sm-2" data-property="rejected.lastyear" data-title="<?= $this->text('admin-projects-rejected-lastyear') ?>"></li>
    </ul>
</div>
