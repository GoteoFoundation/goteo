<?php

$target = $this->target ?: 'raised';
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
    <p><?= $this->text('admin-stats-raised-method-desc') ?></p>

    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.amount_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot4) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.amount_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.amount_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

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
    </ul>


    <h5><?= $this->text('admin-stats-invest-destination') ?></h5>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.to_projects_amount_formatted" data-title="<?= $this->text('admin-stats-to_projects') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.to_projects_amount_formatted" data-title="<?= $this->text('admin-stats-to_projects') . ': ' . $this->text('admin-' . $slot2)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.to_projects_amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.to_projects_amount_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
         <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.to_projects_amount_formatted" data-title="<?= $this->text('admin-stats-to_projects') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
         <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.to_projects_amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.to_projects_amount_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-stats-to_projects') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.to_projects_amount_formatted" data-title="<?= $this->text('admin-stats-to_projects') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.to_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-to_wallet') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.to_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-to_wallet') . ': ' . $this->text('admin-' . $slot2)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.to_wallet_amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.to_wallet_amount_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
         <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.to_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-to_wallet') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
         <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.to_wallet_amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.to_wallet_amount_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-stats-to_wallet') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot3): ?>
         <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.to_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-to_wallet') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

    </ul>

    <h4><?= $this->text('admin-stats-invest-in_wallet') ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_wallet') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.in_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_wallet') . ': ' . $this->text('admin-' . $slot2)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_wallet_amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.in_wallet_amount_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
         <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.in_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_wallet') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
         <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_wallet_amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.in_wallet_amount_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-stats-in_wallet') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.in_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_wallet') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

    </ul>

    <h5><?= $this->text('admin-stats-invest-in_wallet-parts') ?></h5>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_matcher_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_matcher_wallet') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1" ?>.in_matcher_wallet_percent_formatted"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.in_matcher_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_matcher_wallet') . ': ' . $this->text('admin-' . $slot2)  ?>" data-tooltip="<?= "$target.$id.$slot2" ?>.in_matcher_wallet_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_matcher_wallet_amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.in_matcher_wallet_amount_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.in_matcher_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_matcher_wallet') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_matcher_wallet_amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.in_matcher_wallet_amount_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-stats-in_matcher_wallet') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.in_matcher_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_matcher_wallet') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3" ?>.in_matcher_wallet_percent_formatted"></li>
      <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_users_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_users_wallet') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1" ?>.in_users_wallet_percent_formatted"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.in_users_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_users_wallet') . ': ' . $this->text('admin-' . $slot2)  ?>" data-tooltip="<?= "$target.$id.$slot2" ?>.in_users_wallet_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_users_wallet_amount_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.in_users_wallet_amount_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
         <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.in_users_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_users_wallet') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
         <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.in_users_wallet_amount_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.in_users_wallet_amount_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-stats-in_users_wallet') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot3): ?>
         <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.in_users_wallet_amount_formatted" data-title="<?= $this->text('admin-stats-in_users_wallet') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3" ?>.in_users_wallet_percent_formatted"></li>
      <?php endif ?>

    </ul>

</div>
