<?php

$target = 'raised';
$slot1 = 'today';
$slot2 = 'yesterday';
$slot3 = '';
if(in_array($this->interval, ['week', 'month', 'year'])) {
    $slot1 = $this->interval;
    $slot2 = 'last_' . $this->interval;
    $slot3 = 'last_' . $this->interval . '_complete';
}
$id = $this->id ?: 'global';
$method = $this->method ?: $this->text('regular-all');
?>


<div class="d3-chart loading discrete-values" data-source="/api/charts/totals/invests/raised/<?= $id ?>/<?= $slot1 ?>?<?= $this->query ?>" data-interval="40" data-flash-time="15" data-delay="50">
    <h4><?= $this->text("admin-stats-method") ?>: <?= $method ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.amount_formatted" data-title="<?= $this->text('admin-raised') . ': ' .$this->text('admin-' . $slot1) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.<?= $slot2 ?>.amount_formatted" data-title="<?= $this->text('admin-raised') . ': ' .$this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.amount_diff_formatted" data-title="<?= $this->text('admin-raised') . ': ' .$this->text('admin-diff') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.<?= $slot2 ?>.invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.invests_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.invests_diff" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-diff') ?>"></li>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.amount_formatted" data-title="<?= $this->text('admin-raised') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>
    </ul>

    <h4><?= $this->text("admin-stats-from_matchfunding") ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.from_matchfunding_amount_formatted" data-title="<?= $this->text('admin-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.yesterday.from_matchfunding_amount_formatted" data-title="<?= $this->text('admin-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.from_matchfunding_amount_gain_formatted" data-tooltip="raised.<?= $id ?>.today.from_matchfunding_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.from_matcher_amount_formatted" data-title="<?= $this->text('admin-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.yesterday.from_matcher_amount_formatted" data-title="<?= $this->text('admin-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.from_matcher_amount_gain_formatted" data-tooltip="raised.<?= $id ?>.today.from_matcher_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
    </ul>
    <h4><?= $this->text("admin-stats-raised-wallet") ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.to_wallet_amount_formatted" data-title="<?= $this->text('admin-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.yesterday.to_wallet_amount_formatted" data-title="<?= $this->text('admin-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.to_wallet_amount_gain_formatted" data-tooltip="raised.<?= $id ?>.today.to_wallet_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.in_wallet_amount_formatted" data-title="<?= $this->text('admin-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.yesterday.in_wallet_amount_formatted" data-title="<?= $this->text('admin-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.in_wallet_amount_gain_formatted" data-tooltip="raised.<?= $id ?>.today.in_wallet_amount_diff_formatted" data-title="<?= $this->text('admin-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.to_matchers_wallet_amount_formatted" data-title="<?= $this->text('admin-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.yesterday.to_matchers_wallet_amount_formatted" data-title="<?= $this->text('admin-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.to_matchers_wallet_amount_gain_formatted" data-tooltip="raised.global.today.to_matchers_wallet_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
    </ul>
<!--
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.today.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.yesterday.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.week.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-week') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.month.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.year.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.global.last_year_complete.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-last_year') ?>"></li>
    </ul>
-->
<!-- TODO:
    Add %, matchfunding, and other data from the api

    Add selector for method type
 -->
  <?php /* foreach($this->methods as $id => $method): ?>
    <h4><?= $method->getName() ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.today.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-today') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.yesterday.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-yesterday') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.week.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-week') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.month.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-month') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.year.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-year') ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="raised.<?= $id ?>.last_year_complete.amount_formatted" data-title="<?= $this->text('admin-invest-raised-amount-last_year') ?>"></li>
    </ul>
  <?php endforeach */ ?>
</div>
