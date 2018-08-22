<?php

$target = $this->target ?: 'fees';
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

<div class="d3-chart loading discrete-values" data-source="/api/charts/totals/invests/<?= "$target/$id/$slot1" ?>?<?= $query ?>" data-interval="40" data-flash-time="15" data-delay="50">

    <h4><?= $this->text("admin-stats-$target-method") ?>: <?= $method ?></h4>

    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.subtotal_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.subtotal_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.subtotal_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.subtotal_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.subtotal_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot4) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.subtotal_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.subtotal_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.subtotal_formatted" data-title="<?= $this->text('admin-' . $target) . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.vat_formatted" data-title="<?= $this->text('admin-vat') . ': ' . $this->text('admin-' . $slot1) ?>"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.vat_formatted" data-title="<?= $this->text('admin-vat') . ': ' . $this->text('admin-' . $slot2) ?>"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.vat_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.vat_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-vat') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.vat_formatted" data-title="<?= $this->text('admin-vat') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.vat_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.vat_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-vat') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.vat_formatted" data-title="<?= $this->text('admin-vat') . ': ' . $this->text('admin-' . $slot3) ?>"></li>
      <?php endif ?>
    </ul>

    <h5><?= $this->text("admin-stats-fees-parts") ?></h5>

    <ul class="row list-unstyled">
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.user_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1" ?>.user_percent_formatted"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.user_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' . $this->text('admin-' . $slot2) ?>" data-tooltip="<?= "$target.$id.$slot2" ?>.user_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.user_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.user_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.user_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.user_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.user_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.user_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3" ?>.user_percent_formatted"></li>
      <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.call_formatted" data-title="<?= $this->text('admin-call-fee') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1" ?>.call_percent_formatted"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.call_formatted" data-title="<?= $this->text('admin-call-fee') . ': ' . $this->text('admin-' . $slot2) ?>" data-tooltip="<?= "$target.$id.$slot2" ?>.call_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.call_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.call_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-call-fee') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.call_formatted" data-title="<?= $this->text('admin-call-fee') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.call_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.call_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.call_formatted" data-title="<?= $this->text('admin-call-fee') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3" ?>.call_percent_formatted"></li>
      <?php endif ?>

        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.matcher_formatted" data-title="<?= $this->text('admin-matcher-fee') . ': ' . $this->text('admin-' . $slot1) ?>" data-tooltip="<?= "$target.$id.$slot1" ?>.matcher_percent_formatted"></li>
      <?php if($slot2): ?>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot2" ?>.matcher_formatted" data-title="<?= $this->text('admin-matcher-fee') . ': ' . $this->text('admin-' . $slot2) ?>" data-tooltip="<?= "$target.$id.$slot2" ?>.matcher_percent_formatted"></li>
        <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.matcher_<?= $slot2 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.matcher_<?= $slot2 ?>_diff_formatted" data-title="<?= $this->text('admin-matcher-fee') . ': ' . $this->text('admin-diff') ?>"></li>
      <?php endif ?>
      <?php if($slot4): ?>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot4" ?>.matcher_formatted" data-title="<?= $this->text('admin-matcher-fee') . ': ' . $this->text('admin-' . $slot4) ?>"></li>
            <li class="col-xs-2 col-xxs-4" data-property="<?= "$target.$id.$slot1" ?>.matcher_<?= $slot4 ?>_gain_formatted" data-tooltip="<?= "$target.$id.$slot1" ?>.matcher_<?= $slot4 ?>_diff_formatted" data-title="<?= $this->text('admin-user-fee') . ': ' .$this->text('admin-diff') ?>"></li>
      <?php endif ?>

      <?php if($slot3): ?>
            <li class="col-xs-2 col-xxs-4<?= $slot4 ? '' : ' col-xs-offset-4'?>" data-property="<?= "$target.$id.$slot3" ?>.matcher_formatted" data-title="<?= $this->text('admin-matcher-fee') . ': ' . $this->text('admin-' . $slot3) ?>" data-tooltip="<?= "$target.$id.$slot3" ?>.matcher_percent_formatted"></li>
      <?php endif ?>
    </ul>

</div>
