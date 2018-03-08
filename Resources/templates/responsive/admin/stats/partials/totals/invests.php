<div class="d3-chart discrete-values" data-source="/api/charts/totals/invests?<?= $this->query ?>" data-interval="15" data-flash-time="15" data-delay="50">
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="global.raised.today.amount" data-title="<?= $this->text('admin-invest-raised-amount-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="global.raised.yesterday.amount" data-title="<?= $this->text('admin-invest-raised-amount-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="global.raised.week.amount" data-title="<?= $this->text('admin-invest-raised-amount-week') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="global.raised.month.amount" data-title="<?= $this->text('admin-invest-raised-amount-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="global.raised.year.amount" data-title="<?= $this->text('admin-invest-raised-amount-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="global.raised.lastyear.amount" data-title="<?= $this->text('admin-invest-raised-amount-lastyear') ?>"></li>
    </ul>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="global.commissions.today" data-title="<?= $this->text('admin-invest-commissions-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="global.commissions.yesterday" data-title="<?= $this->text('admin-invest-commissions-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="global.commissions.week" data-title="<?= $this->text('admin-invest-commissions-week') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="global.commissions.month" data-title="<?= $this->text('admin-invest-commissions-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="global.commissions.year" data-title="<?= $this->text('admin-invest-commissions-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="global.commissions.lastyear" data-title="<?= $this->text('admin-invest-commissions-lastyear') ?>"></li>
    </ul>
    <ul class="row list-unstyled">
        <?php if($this->get_config('payments.paypal.active')): ?>
        <li class="col-xs-2 col-xxs-4" data-property="payments.paypal.commissions.month" data-title="<?= $this->text('admin-invest-commissions-paypal-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="payments.paypal.commissions.year" data-title="<?= $this->text('admin-invest-commissions-paypal-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="payments.paypal.commissions.lastyear" data-title="<?= $this->text('admin-invest-commissions-paypal-lastyear') ?>"></li>
        <?php endif ?>
        <?php if($this->get_config('payments.tpv.active')): ?>
        <li class="col-xs-2 col-xxs-4" data-property="payments.tpv.commissions.month" data-title="<?= $this->text('admin-invest-commissions-tpv-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="payments.tpv.commissions.year" data-title="<?= $this->text('admin-invest-commissions-tpv-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="payments.tpv.commissions.lastyear" data-title="<?= $this->text('admin-invest-commissions-tpv-lastyear') ?>"></li>
        <?php endif ?>
    </ul>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="fees.month" data-title="<?= $this->text('admin-invest-fees-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="fees.year" data-title="<?= $this->text('admin-invest-fees-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="fees.lastyear" data-title="<?= $this->text('admin-invest-fees-lastyear') ?>"></li>
    </ul>
</div>
