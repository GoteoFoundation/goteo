<?php

$target = 'raised';
$slot1 = $this->interval ?: 'today';
$slot2 = $slot1 === 'custom' ? '' : 'yesterday';
$slot3 = '';
if(in_array($this->interval, ['week', 'month', 'year'])) {
    $slot2 = 'last_' . $this->interval;
    $slot3 = 'last_' . $this->interval . '_complete';
}
$id = $this->id ?: 'global';
$method = $this->method ?: $this->text('regular-all');
?>


<div class="d3-chart loading discrete-values" data-source="/api/charts/totals/invests/raised/<?= $id ?>/<?= $slot1 ?>?<?= $this->query ?>" data-interval="40" data-flash-time="15" data-delay="50">

    <h4><?= $this->text('admin-stats-method') ?>: <?= $method ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.amount_diff_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' .$this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.invests_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.invests_diff" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>
    </ul>

    <h4><?= $this->text('admin-stats-from_matchfunding') ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.from_matchfunding_amount_formatted" data-title="<?= $this->text('admin-stats-matchfunding-part') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.from_matchfunding_amount_formatted" data-title="<?= $this->text('admin-stats-matchfunding-part') . ': ' . $this->text('admin-' . $slot2)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.from_matchfunding_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.from_matchfunding_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.from_matchfunding_amount_formatted" data-title="<?= $this->text('admin-stats-matchfunding-part') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.from_matcher_amount_formatted" data-title="<?= $this->text('admin-stats-matcher-part') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.from_matcher_amount_formatted" data-title="<?= $this->text('admin-stats-matcher-part') . ': ' . $this->text('admin-' . $slot2)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.from_matcher_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.from_matcher_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.from_matcher_amount_formatted" data-title="<?= $this->text('admin-stats-matcher-part') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>

    </ul>

<?php if($slot1 !== 'custom'): ?>
    <h4><?= $this->text('admin-stats-invest-destination') ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.to_projects_amount_formatted" data-title="<?= $this->text('admin-stats-to_projects') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.to_projects_amount_formatted" data-title="<?= $this->text('admin-stats-to_projects') . ': ' . $this->text('admin-' . $slot2)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.to_projects_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.to_projects_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.to_projects_amount_formatted" data-title="<?= $this->text('admin-stats-to_projects') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.to_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-to_wallet') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.to_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-to_wallet') . ': ' . $this->text('admin-' . $slot2)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.to_wallet_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.to_wallet_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.to_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-to_wallet') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>

    </ul>

    <h4><?= $this->text('admin-stats-invest-in_wallet') ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_wallet') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.in_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_wallet') . ': ' . $this->text('admin-' . $slot2)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_wallet_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.in_wallet_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.in_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_wallet') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_matcher_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_matcher_wallet') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.in_matcher_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_matcher_wallet') . ': ' . $this->text('admin-' . $slot2)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_matcher_wallet_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.from_matcher_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3" ?>.in_matcher_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_matcher_wallet') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>

    </ul>
    <?php endif ?>

</div>
