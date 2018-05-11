<?php
$query = $this->raw('query');
?>
<div class="d3-chart loading discrete-values" data-source="/api/charts/totals/projects?<?= $query ?>" data-interval="15" data-flash-time="30" data-delay="50">

    <ul class="row list-unstyled">
        <li class="col-xxs-4 col-xs-2" data-property="created.today" data-title="<?= $this->text('admin-projects-created-today') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="created.yesterday" data-title="<?= $this->text('admin-projects-created-yesterday') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="created.week" data-title="<?= $this->text('admin-projects-created-week') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="created.month" data-title="<?= $this->text('admin-projects-created-month') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="created.year" data-title="<?= $this->text('admin-projects-created-year') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="created.last_year" data-title="<?= $this->text('admin-projects-created-last_year') ?>"></li>
    </ul>

    <ul class="row list-unstyled">
        <li class="col-xxs-4 col-xs-2" data-property="negotiating.today" data-title="<?= $this->text('admin-projects-negotiating-today') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="negotiating.yesterday" data-title="<?= $this->text('admin-projects-negotiating-yesterday') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="negotiating.week" data-title="<?= $this->text('admin-projects-negotiating-week') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="negotiating.month" data-title="<?= $this->text('admin-projects-negotiating-month') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="negotiating.year" data-title="<?= $this->text('admin-projects-negotiating-year') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="negotiating.last_year" data-title="<?= $this->text('admin-projects-negotiating-last_year') ?>"></li>
    </ul>


    <ul class="row list-unstyled">
        <li class="col-xxs-4 col-xs-2" data-property="reviewing.today" data-title="<?= $this->text('admin-projects-review-today') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="reviewing.yesterday" data-title="<?= $this->text('admin-projects-review-yesterday') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="reviewing.week" data-title="<?= $this->text('admin-projects-review-week') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="reviewing.month" data-title="<?= $this->text('admin-projects-review-month') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="reviewing.year" data-title="<?= $this->text('admin-projects-review-year') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="reviewing.last_year" data-title="<?= $this->text('admin-projects-review-last_year') ?>"></li>
    </ul>

    <ul class="row list-unstyled">
        <li class="col-xxs-4 col-xs-2" data-property="published.today" data-title="<?= $this->text('admin-projects-published-today') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="published.yesterday" data-title="<?= $this->text('admin-projects-published-yesterday') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="published.week" data-title="<?= $this->text('admin-projects-published-week') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="published.month" data-title="<?= $this->text('admin-projects-published-month') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="published.year" data-title="<?= $this->text('admin-projects-published-year') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="published.last_year" data-title="<?= $this->text('admin-projects-published-last_year') ?>"></li>
    </ul>

    <ul class="row list-unstyled">
        <li class="col-xxs-4 col-xs-2" data-property="rejected.today" data-title="<?= $this->text('admin-projects-rejected-today') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="rejected.yesterday" data-title="<?= $this->text('admin-projects-rejected-yesterday') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="rejected.week" data-title="<?= $this->text('admin-projects-rejected-week') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="rejected.month" data-title="<?= $this->text('admin-projects-rejected-month') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="rejected.year" data-title="<?= $this->text('admin-projects-rejected-year') ?>"></li>
        <li class="col-xxs-4 col-xs-2" data-property="rejected.last_year" data-title="<?= $this->text('admin-projects-rejected-last_year') ?>"></li>
    </ul>
</div>
