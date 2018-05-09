<?php

$target = $this->target ?: 'fees';
$slot1 = $this->interval ?: 'today';
$slot2 = $slot1 === 'custom' ? '' : 'yesterday';
$slot3 = '';
if(in_array($this->interval, ['week', 'month', 'year'])) {
    $slot2 = 'last_' . $this->interval;
    $slot3 = 'last_' . $this->interval . '_complete';
}
$id = $this->id ?: 'global';
$method = $this->method ?: $this->text('regular-all');
$query = $this->raw('query');
?>

<div class="d3-chart loading discrete-values" data-source="/api/charts/totals/invests/<?= "$target/$id/$slot1" ?>?<?= $query ?>" data-interval="40" data-flash-time="15" data-delay="50">

    <h4><?= $this->text("admin-stats-$target-method") ?>: <?= $method ?></h4>

    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.subtotal_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.subtotal_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.subtotal_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.subtotal_diff_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' .$this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.subtotal_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.vat_formatted" data-title="<?= $this->text('admin-vat') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.vat_formatted" data-title="<?= $this->text('admin-vat') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.vat_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.vat_diff_formatted" data-title="<?= $this->text('admin-vat') . ': ' . $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.vat_formatted" data-title="<?= $this->text('admin-vat') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>
    </ul>

    <h5><?= $this->text("admin-stats-fees-parts") ?></h5>

    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.user_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1" ?>.user_percent_formatted"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.user_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' . $this->text('admin-' . $slot2) ?>" data-tooltip="<?= "$target.$id.$slot2" ?>.user_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.user_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.user_diff_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' .$this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.user_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3" ?>.user_percent_formatted"></li>
        <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.call_formatted" data-title="<?= $this->text('admin-call-fee') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1" ?>.call_percent_formatted"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.call_formatted" data-title="<?= $this->text('admin-call-fee') . ': ' . $this->text('admin-' . $slot2) ?>" data-tooltip="<?= "$target.$id.$slot2" ?>.call_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.call_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.call_diff_formatted" data-title="<?= $this->text('admin-call-fee') . ': ' . $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.call_formatted" data-title="<?= $this->text('admin-call-fee') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3" ?>.call_percent_formatted"></li>
        <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.matcher_formatted" data-title="<?= $this->text('admin-matcher-fee') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1" ?>.matcher_percent_formatted"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.matcher_formatted" data-title="<?= $this->text('admin-matcher-fee') . ': ' . $this->text('admin-' . $slot2) ?>" data-tooltip="<?= "$target.$id.$slot2" ?>.matcher_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.matcher_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.matcher_diff_formatted" data-title="<?= $this->text('admin-matcher-fee') . ': ' . $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.matcher_formatted" data-title="<?= $this->text('admin-matcher-fee') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3" ?>.matcher_percent_formatted"></li>
        <?php endif ?>
    </ul>

</div>
