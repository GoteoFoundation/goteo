<?php
$query = $this->raw('query');
?>
<div class="row">
    <ul class="row list-unstyled d3-chart loading discrete-values" data-source="/api/charts/totals/invests/commissions?<?= $query ?>" data-interval="40" data-interval-delay="10" data-flash-time="15" data-delay="50">
        <li class="col-xs-2 col-xxs-4" data-property="commissions.global.today.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.global.yesterday.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.global.week.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-week') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.global.month.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.global.year.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.global.last_year.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-last_year') ?>"></li>
    </ul>
    <?php if($this->get_config('payments.paypal.active')): ?>
    <ul class="row list-unstyled d3-chart loading discrete-values" data-source="/api/charts/totals/invests/commissions/paypal?<?= $query ?>" data-interval="40" data-interval-delay="20" data-flash-time="15" data-delay="50">
        <li class="col-xs-2 col-xxs-4" data-property="commissions.paypal.month.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-paypal-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.paypal.month.lost_formatted" data-title="<?= $this->text('admin-invest-commissions-lost-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.paypal.year.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-paypal-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.paypal.year.lost_formatted" data-title="<?= $this->text('admin-invest-commissions-lost-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.paypal.last_year.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-paypal-last_year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.paypal.year.charged_gain_formatted" data-title="<?= $this->text('admin-invest-diff-last_year') ?>"></li>
    </ul>
    <?php endif ?>
    <?php if($this->get_config('payments.tpv.active')): ?>
    <ul class="row list-unstyled d3-chart loading discrete-values" data-source="/api/charts/totals/invests/commissions/tpv?<?= $query ?>" data-interval="40" data-interval-delay="20" data-flash-time="15" data-delay="50">
        <li class="col-xs-2 col-xxs-4" data-property="commissions.tpv.month.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-tpv-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.tpv.month.lost_formatted" data-title="<?= $this->text('admin-invest-commissions-lost-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.tpv.year.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-tpv-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.tpv.year.lost_formatted" data-title="<?= $this->text('admin-invest-commissions-lost-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.tpv.last_year.charged_formatted" data-title="<?= $this->text('admin-invest-commissions-paypal-last_year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="commissions.tpv.year.charged_gain_formatted" data-title="<?= $this->text('admin-invest-diff-last_year') ?>"></li>
    </ul>
    <?php endif ?>
    <ul class="row list-unstyled d3-chart loading discrete-values" data-source="/api/charts/totals/invests/fees?<?= $query ?>" data-interval="40" data-interval-delay="30" data-flash-time="15" data-delay="50">
        <li class="col-xs-2 col-xxs-4" data-property="fees.global.month.total_formatted" data-title="<?= $this->text('admin-invest-fees-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="fees.global.last_month.total_formatted" data-title="<?= $this->text('admin-invest-fees-last_month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="fees.global.month.total_gain_formatted" data-title="<?= $this->text('admin-invest-diff-last_month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="fees.global.year.total_formatted" data-title="<?= $this->text('admin-invest-fees-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="fees.global.last_year.total_formatted" data-title="<?= $this->text('admin-invest-fees-last_year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="fees.global.year.total_gain_formatted" data-title="<?= $this->text('admin-invest-diff-last_year') ?>"></li>
    </ul>
</div>
