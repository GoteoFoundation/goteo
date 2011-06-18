<?php

$project    = $this['project'];

$minimum    = $project->mincost;
$optimum    = $project->maxcost;
?>        
    <div class="meter">
        <dl>            
            <dt class="minimum" style="width: <?php echo number_format(($minimum / $optimum) * 100) ?>%"><span><?php echo Text::get('project-view-metter-minimum'); ?></span></dt>
            <dd class="minimum" style="width: <?php echo number_format(($minimum / $optimum) * 100) ?>%"><span><strong><?php echo number_format($minimum) ?></strong> <span class="euro">&euro;</span></span></dd>   
            <dt class="optimum"><span><?php echo Text::get('project-view-metter-optimum'); ?></span></dt>
            <dd class="optimum"><strong><?php echo number_format($optimum) ?></strong> <span class="euro">&euro;</span></dd>
        </dl>
        
   </div>   