<div class="row">
    <ul class="row list-unstyled d3-chart loading discrete-values" data-source="/api/charts/totals/invests/raised?<?= $this->query ?>" data-interval="40" data-flash-time="15" data-delay="50">
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.today.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.yesterday.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.week.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-week') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.month.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.year.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.last_year_complete.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-last_year') ?>"></li>
    </ul>
    <ul class="row list-unstyled d3-chart loading discrete-values" data-source="/api/charts/totals/invests/active?<?= $this->query ?>" data-interval="40" data-flash-time="15" data-delay="50">
        <li class="col-xs-2 col-xxs-4" data-property="active.global.today.amount_formatted" data-title="<?= $this->text('admin-invest-active-amount-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="active.global.yesterday.amount_formatted" data-title="<?= $this->text('admin-invest-active-amount-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="active.global.week.amount_formatted" data-title="<?= $this->text('admin-invest-active-amount-week') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="active.global.month.amount_formatted" data-title="<?= $this->text('admin-invest-active-amount-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="active.global.year.amount_formatted" data-title="<?= $this->text('admin-invest-active-amount-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="active.global.last_year_complete.amount_formatted" data-title="<?= $this->text('admin-invest-active-amount-last_year') ?>"></li>
    </ul>
</div>
