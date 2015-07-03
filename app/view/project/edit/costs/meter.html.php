<?php

use Goteo\Library\Text,
	Goteo\Library\Currency;

$project    = $vars['project'];

//moneda actual
$select_currency=Currency::$currencies[$project->currency]['html'];

$minimum    = $project->mincost;
$optimum    = $project->maxcost;
?>
    <div class="meter">
        <dl>
            <dt class="minimum" style="width: <?php echo @number_format(($minimum / $optimum) * 100) ?>%"><span><?php echo Text::get('project-view-metter-minimum'); ?></span></dt>
            <dd class="minimum" style="width: <?php echo @number_format(($minimum / $optimum) * 100) ?>%"><span><strong><?php echo $select_currency." ".number_format($minimum) ?></strong></span></dd>
            <dt class="optimum"><span><?php echo Text::get('project-view-metter-optimum'); ?></span></dt>
            <dd class="optimum"><strong><?php echo $select_currency." ".number_format($optimum) ?></strong></dd>
        </dl>

   </div>
