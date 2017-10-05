<?php

$min = $this->minimum ? $this->minimum : '0';
$opt = $this->optimum ? $this->optimum : '0';
$left = round(100 * $min / ($min + $opt));
$right = round(100 * $opt / ($min + $opt));

?><div class="progress">
  <div class="progress-bar minimum progress-bar-cyan" style="width: <?= $left ?>%">
    <?= $this->text('project-view-metter-minimum') ?> <span><?= euro_format($min) ?></span> <?= $this->currency($this->project->currency) ?>
  </div>
  <div class="progress-bar optimum progress-bar-lilac" style="width: <?= $right ?>%">
    <?= $this->text('project-view-metter-optimum') ?> <span><?= euro_format($opt) ?></span> <?= $this->currency($this->project->currency) ?>
  </div>
</div>
