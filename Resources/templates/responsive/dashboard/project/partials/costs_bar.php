<?php

$min = $this->minimum ? $this->minimum : '0';
$opt = $this->optimum ? $this->optimum : '0';
$total = $min + $opt;
$right = $left = 0;
if($total) {
    $left = round(100 * $min / $total);
    $right = round(100 * $opt / $total);
}

$last_min_width = 101;
$last_width = 25;
?>

<div class="costs-bar spacer-10">
    <div class="min">
        <?= $this->text('project-view-metter-minimum') ?>
         <span><?= $this->currency($this->project->currency) ?> <strong class="amount-min"><?= euro_format($min) ?></strong></span>
    </div>
    <div class="opt">
        <?= $this->text('project-view-metter-optimum') ?>
         <span><?= $this->currency($this->project->currency) ?> <strong class="amount-opt"><?= euro_format($opt) ?></strong></span>
    </div>
    <div class="total">
        <?= $this->text('regular-total') ?>
         <span><?= $this->currency($this->project->currency) ?> <strong class="amount-total"><?= euro_format($opt + $min) ?></strong></span>
    </div>

    <div class="progress">
      <div class="progress-bar bar-min progress-bar-lilac" style="width: <?= $left ?>%">
        <?= $left ?> %
      </div>
      <div class="progress-bar bar-opt progress-bar-cyan" style="width: <?= $right ?>%">
        <?= $right ?> %
      </div>
    </div>
</div>
