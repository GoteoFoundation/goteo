<?php

$target = 'matchfunding';
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


<div class="d3-chart loading discrete-values" data-source="/api/charts/totals/invests/<?= $target ?>/<?= $id ?>/<?= $slot1 ?>?<?= $query ?>" data-interval="40" data-flash-time="15" data-delay="50">
<?php foreach(['raised', 'active'] as $type): ?>
    <h2><?= $this->text('admin-stats-'.$type) ?></h2>
    <blockquote>
    <h4><?= $this->text('admin-stats-matchfunding-global') ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1.$type" ?>_matchfunding_amount_formatted" data-title="<?= $this->text('admin-stats-matchfunding-part') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1.$type" ?>_matchfunding_amount_percent_formatted"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2.$type" ?>_matchfunding_amount_formatted" data-title="<?= $this->text('admin-stats-matchfunding-part') . ': ' . $this->text('admin-' . $slot2)  ?>" data-tooltip="<?= "$target.$id.$slot2.$type" ?>_matchfunding_amount_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1.$type" ?>_matchfunding_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1.$type" ?>_matchfunding_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3.$type" ?>_matchfunding_amount_formatted" data-title="<?= $this->text('admin-stats-matchfunding-part') . ': ' . $this->text('admin-' . $slot3) ?>"  data-tooltip="<?= "$target.$id.$slot3.$type" ?>_matchfunding_amount_percent_formatted"></li>
        <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1.$type" ?>_users_amount_formatted" data-title="<?= $this->text('admin-stats-users-part') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1.$type" ?>_users_amount_percent_formatted"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2.$type" ?>_users_amount_formatted" data-title="<?= $this->text('admin-stats-users-part') . ': ' . $this->text('admin-' . $slot2)  ?>" data-tooltip="<?= "$target.$id.$slot2.$type" ?>_users_amount_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1.$type" ?>_users_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1.$type" ?>_users_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3.$type" ?>_users_amount_formatted" data-title="<?= $this->text('admin-stats-users-part') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3.$type" ?>_users_amount_percent_formatted"></li>
        <?php endif ?>

    </ul>

    <h4><?= $this->text('admin-stats-from_matchfunding') ?></h4>
    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1.$type" ?>_from_matchfunding_amount_formatted" data-title="<?= $this->text('admin-stats-match-part') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1.$type" ?>_from_matchfunding_amount_percent_formatted"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2.$type" ?>_from_matchfunding_amount_formatted" data-title="<?= $this->text('admin-stats-match-part') . ': ' . $this->text('admin-' . $slot2)  ?>" data-tooltip="<?= "$target.$id.$slot2.$type" ?>_from_matchfunding_amount_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1.$type" ?>_from_matchfunding_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1.$type" ?>_from_matchfunding_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3.$type" ?>_from_matchfunding_amount_formatted" data-title="<?= $this->text('admin-stats-match-part') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3.$type" ?>_from_matchfunding_amount_percent_formatted"></li>
        <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1.$type" ?>_from_users_amount_formatted" data-title="<?= $this->text('admin-stats-citizens-part') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1.$type" ?>_from_users_amount_percent_formatted"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2.$type" ?>_from_users_amount_formatted" data-title="<?= $this->text('admin-stats-citizens-part') . ': ' . $this->text('admin-' . $slot2)  ?>" data-tooltip="<?= "$target.$id.$slot2.$type" ?>_from_users_amount_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1.$type" ?>_from_users_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1.$type" ?>_from_users_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3.$type" ?>_from_users_amount_formatted" data-title="<?= $this->text('admin-stats-citizens-part') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3.$type" ?>_from_users_amount_percent_formatted"></li>
        <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1.$type" ?>_from_matcher_amount_formatted" data-title="<?= $this->text('admin-stats-matcher-part') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
        <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2.$type" ?>_from_matcher_amount_formatted" data-title="<?= $this->text('admin-stats-matcher-part') . ': ' . $this->text('admin-' . $slot2)  ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1.$type" ?>_from_matcher_amount_gain_formatted" data-tooltip="<?= "$target.$id.$slot1.$type" ?>_from_matcher_amount_diff_formatted" data-title="<?= $this->text('admin-diff') ?>"></li>
        <?php endif ?>
        <?php if($slot3): ?>
            <li class="col-xs-2 col-xs-offset-4 col-xxs-4" data-property="<?= "$target.$id.$slot3.$type" ?>_from_matcher_amount_formatted" data-title="<?= $this->text('admin-stats-matcher-part') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
        <?php endif ?>

    </ul>
</blockquote>
<?php endforeach ?>
</div>
