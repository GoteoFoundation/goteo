<?php

$min = $this->minimum ? $this->minimum : '0';
$opt = $this->optimum ? $this->optimum : '0';
$total = $min + $opt;
$right = $left = 0;
if($total) {
    $left = round(100 * $min / $total);
    $right = round(100 * $opt / $total);
}

?>

<div class="costs-bar spacer-10">
    <div class="min" style="width: <?= $left ?>%">
        <?= $this->text('project-view-metter-minimum') ?>
         <span><?= $this->currency($this->project->currency) ?> <strong class="amount-min"><?= euro_format($min) ?></strong></span>
    </div>
    <div class="opt" style="width: <?= $right - 20 ?>%; width: calc(<?= $right ?>% - 101px);">
        <?= $this->text('project-view-metter-optimum') ?>
         <span><?= $this->currency($this->project->currency) ?> <strong class="amount-opt"><?= euro_format($opt) ?></strong></span>
    </div>
    <div class="total" style="width: <?= 20 ?>%; width: 101px;">
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
