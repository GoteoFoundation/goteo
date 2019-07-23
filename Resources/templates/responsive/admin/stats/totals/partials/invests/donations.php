<?php

$target = $this->target ?: 'donations';
$slot1 = $this->interval ?: 'today';
$slot2 = $slot1 === 'custom' ? '' : 'yesterday';
$slot3 = $slot4 = '';
if(in_array($this->interval, ['week', 'month', 'year'])) {
    $slot2 = 'last_' . $this->interval;
    $slot3 = 'last_' . $this->interval . '_complete';
    if($this->interval !== 'year')
        $slot4 = 'last_year_' . $this->interval;
}
$id = $this->id ?: 'global';
$method = $this->method ?: $this->text('regular-all');
$query = $this->raw('query');
?>


<div class="d3-chart loading discrete-values" data-source="/api/charts/totals/invests/<?= $target ?>/<?= $id ?>/<?= $slot1 ?>?<?= $query ?>" data-interval="40" data-flash-time="15" data-delay="50">

    <h4><?= $this->text("admin-stats-$target-method") ?>: <?= $method ?></h4>
    <p><?= $this->text('admin-stats-donations-method-desc') ?></p>

    <ul class="row list-unstyled">
        <!-- donations amount -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.donations_amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.donations_amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.donations_amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.donations_amount_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.donations_amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot4) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.donations_amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.donations_amount_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.donations_amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

        <!-- num. invests  -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.invests_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.invests_<?= $slot2 ?>_diff" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.invests_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.invests_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-invests') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

      <!-- Num. users with donations  -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.users_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.users_<?= $slot2 ?>_diff" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.users_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.users_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-users') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

    </ul>

    <h5><?= $this->text("admin-stats-donations-tip") ?></h5>
    <!-- Donations from projects -->
    <ul class="row list-unstyled">
        <!-- donations amount -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_amount_formatted" data-title="<?= $this->text('admin-donations-tip') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1" ?>.tip_percent_formatted"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.tip_amount_formatted" data-title="<?= $this->text('admin-donations-tip') . ': ' . $this->text('admin-' . $slot2) ?>" data-tooltip="<?= "$target.$id.$slot2" ?>.tip_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.tip_amount_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-donations-tip') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.tip_amount" data-title="<?= $this->text('admin-donations-tip') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.tip_amount_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.tip_amount_formatted" data-title="<?= $this->text('admin-donations-tip') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3" ?>.tip_percent_formatted"></li>
      <?php endif ?>

        <!-- num. invests  -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_invests" data-title="<?= $this->text('admin-donations-tip-invests') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.tip_invests" data-title="<?= $this->text('admin-donations-tip-invests') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_invests_<?= $slot2 ?>_gain_formatted"" data-title="<?= $this->text('admin-donations-tip-invests') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.tip_invests" data-title="<?= $this->text('admin-donations-tip-invests') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_invests_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.tip_invests_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.tip_invests" data-title="<?= $this->text('admin-donations-tip-invests') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

      <!-- Num. users with donations  -->
      <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_users" data-title="<?= $this->text('admin-donations-tip-users') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.tip_users" data-title="<?= $this->text('admin-donations-tip-users') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_users_<?= $slot2 ?>_gain_formatted"" data-title="<?= $this->text('admin-donations-tip-users') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.tip_users" data-title="<?= $this->text('admin-donations-tip-users') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_users_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.tip_users_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.tip_users" data-title="<?= $this->text('admin-donations-tip-users') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>


    </ul>

    <h5><?= $this->text("admin-stats-donations-direct") ?></h5>
    <!-- Direct Donations -->
    <ul class="row list-unstyled">
        <!-- donations amount -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_amount_formatted" data-title="<?= $this->text('admin-donations-direct') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.direct_amount_formatted" data-title="<?= $this->text('admin-donations-direct') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.direct_amount_<?= $slot2 ?>_diff" data-title="<?= $this->text('admin-donations-direct') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.direct_amount_formatted" data-title="<?= $this->text('admin-donations-direct') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.direct_amount_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.direct_amount_formatted" data-title="<?= $this->text('admin-donations-direct') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

        <!-- num. invests  -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.direct_invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_invests_<?= $slot2 ?>_gain_formatted" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.direct_invests" data-title="<?= $this->text('admin-stats-direct') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_invests_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.direct_invests_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.direct_invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

      <!-- Num. users with donations  -->
      <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.direct_users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_users_<?= $slot2 ?>_gain_formatted" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.direct_users" data-title="<?= $this->text('admin-stats-direct') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_users_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.direct_users_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.direct_users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

    </ul>

    <h5><?= $this->text("admin-stats-donations-generosity") ?></h5>
    <p><?= $this->text("admin-stats-donations-generosity-desc") ?></p>

    <!-- Direct Donations -->
    <ul class="row list-unstyled">
        <!-- percent donations vs total amount -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.donations_amount_percent_formatted" data-title="<?= $this->text('admin-donations-amount-percent') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.donations_amount_percent_formatted" data-title="<?= $this->text('admin-donations-amount-percent') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.donations_amount_percent_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.donations_amount_percent_<?= $slot2 ?>_diff" data-title="<?= $this->text('admin-donations-amount-percent') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.donations_amount_percent_formatted" data-title="<?= $this->text('admin-donations-amount-percent') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? ' col-xs-offset-2' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.donations_amount_percent_formatted" data-title="<?= $this->text('admin-donations-amount-percent') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

      <!-- percent donations via tip vs total amount -->
      <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_amount_percent_formatted" data-title="<?= $this->text('admin-donations-tip-percent') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.tip_amount_percent_formatted" data-title="<?= $this->text('admin-donations-tip-percent') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.tip_amount_percent_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.tip_amount_percent_<?= $slot2 ?>_diff" data-title="<?= $this->text('admin-donations-tip-percent') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.tip_amount_percent_formatted" data-title="<?= $this->text('admin-donations-tip-percent') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? ' col-xs-offset-2' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.tip_amount_percent_formatted" data-title="<?= $this->text('admin-donations-tip-percent') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

      <!-- percent directo donations vs total amount -->
      <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_amount_percent_formatted" data-title="<?= $this->text('admin-donations-direct-percent') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.direct_amount_percent_formatted" data-title="<?= $this->text('admin-donations-direct-percent') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.direct_amount_percent_<?= $slot2 ?>_gain_formatted" data-tooldirect="<?= "$target.$id.$slot1" ?>.direct_amount_percent_<?= $slot2 ?>_diff" data-title="<?= $this->text('admin-donations-direct-percent') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.direct_amount_percent_formatted" data-title="<?= $this->text('admin-donations-direct-percent') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? ' col-xs-offset-2' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.direct_amount_percent_formatted" data-title="<?= $this->text('admin-donations-direct-percent') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>
    </ul>

    <h5><?= $this->text("admin-stats-donations-expired") ?></h5>
    <!-- Direct Donations -->
    <ul class="row list-unstyled">
        <!-- donations amount -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.expired_amount_formatted" data-title="<?= $this->text('admin-donations-expired') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.expired_amount_formatted" data-title="<?= $this->text('admin-donations-expired') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.expired_amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.expired_amount_<?= $slot2 ?>_diff" data-title="<?= $this->text('admin-donations-expired') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.expired_amount_formatted" data-title="<?= $this->text('admin-stats-expired') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.expired_amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.expired_amount_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.expired_amount_formatted" data-title="<?= $this->text('admin-donations-expired') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

        <!-- num. invests  -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.expired_invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.expired_invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.expired_invests_<?= $slot2 ?>_gain_formatted" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.expired_invests" data-title="<?= $this->text('admin-stats-expired') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.expired_invests_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.expired_invests_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.expired_invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

      <!-- Num. users with donations  -->
      <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.expired_users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.expired_users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.expired_users_<?= $slot2 ?>_gain_formatted" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.expired_users" data-title="<?= $this->text('admin-stats-expired') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.expired_users_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.expired_users_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.expired_users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

    </ul>


    <h5><?= $this->text("admin-stats-donations-refunded") ?></h5>
    <!-- Direct Donations -->
    <ul class="row list-unstyled">
        <!-- donations amount -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.refunded_amount_formatted" data-title="<?= $this->text('admin-donations-refunded') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.refunded_amount_formatted" data-title="<?= $this->text('admin-donations-refunded') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.refunded_amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.refunded_amount_<?= $slot2 ?>_diff" data-title="<?= $this->text('admin-donations-refunded') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.refunded_amount_formatted" data-title="<?= $this->text('admin-stats-refunded') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.refunded_amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.refunded_amount_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.refunded_amount_formatted" data-title="<?= $this->text('admin-donations-refunded') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

        <!-- num. invests  -->
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.refunded_invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.refunded_invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.refunded_invests_<?= $slot2 ?>_gain_formatted" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.refunded_invests" data-title="<?= $this->text('admin-stats-refunded') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.refunded_invests_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.refunded_invests_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.refunded_invests" data-title="<?= $this->text('admin-invests') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

      <!-- Num. users with donations  -->
      <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.refunded_users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.refunded_users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.refunded_users_<?= $slot2 ?>_gain_formatted" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.refunded_users" data-title="<?= $this->text('admin-stats-refunded') . ': ' . $this->text('admin-' . $slot4)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.refunded_users_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.refunded_users_<?= $slot4 ?>_diff" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.refunded_users" data-title="<?= $this->text('admin-users') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

    </ul>

</div>
