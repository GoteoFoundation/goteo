<?php
use Goteo\Library\Text;

$level = (int) $this['level'] ?: 3;

$horizontal = !empty($this['horizontal']);

$project = $this['project'];

$minimum    = $project->mincost;
$optimum    = $project->maxcost;
$reached    = $project->invested;
$supporters = count($project->investors);
$days       = $project->days;


// PHP la pifia (y mucho) con los cálculos en coma flotante
if ($reached >= $minimum) {

    $donemax = $horizontal ? 160 : 140;
//    $minimum_done = 100;
    $minimum_done = min($donemax, round(($reached / $minimum) * 100));
    $minimum_done_per = round(($reached / $minimum) * 100);
    $minimum_left = 0;
    
} else {
    
    $minimum_done = min(100, round(($reached / $minimum) * 100));
    $minimum_done_per = round(($reached / $minimum) * 100);
    $minimum_left = max(0, round((1 - $reached / $minimum) * 100));
    
    if ($minimum_done >= 100) {
        // No muestres 100 si falta aunque sea un céntimo
        $minimum_done = 99;
    }
}

$more  = $optimum - $minimum;
$over = $reached - $minimum;

if ($over > 0) {
    
    if ($over >= $more) {
        $optimum_done = 100;
    } else {
        $optimum_done = min(100, round($over / ($optimum - $minimum)));
        
        if ($optimum_done >= 100) {
            $optimum_done = 99;
        }
    }    
    
} else {
    $optimum_done = 0;
}

$optimum_left = 100 - $optimum_done;

$minimum_ratio =  min(100, round(($minimum / $optimum) * 100));

?>        
    
    <div class="meter <?php echo $horizontal ? 'hor' : 'ver' ?>">
        
        <h<?php echo $level ?> class="title"><?php echo Text::get('project-view-metter-investment'); ?></h<?php echo $level ?>>
    
        <div class="graph">            
            <div class="optimum">
                 <div class="left" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($optimum_left) ?>%"></div>
                 <div class="done" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($optimum_done) ?>%"></div>
            </div>
            <div class="minimum" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_ratio) ?>%">
                <div class="left" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_left) ?>%"><strong><?php echo number_format($minimum_left) ?>%</strong></div>
                <div class="done" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_done) ?>%"><strong><?php echo number_format($minimum_done_per) ?>%</strong></div>
            </div>
        </div>

        <dl>
            <dt class="optimum"><?php echo Text::get('project-view-metter-optimum'); ?></dt>
            <dd class="optimum"><strong><?php echo number_format($optimum) ?></strong> <span class="euro">&euro;</span></dd>

            <dt class="minimum" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_ratio) ?>%"><span><?php echo Text::get('project-view-metter-minimum'); ?></span></dt>
            <dd class="minimum" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($minimum_ratio) ?>%"><strong><?php echo number_format($minimum) ?> <span class="euro">&euro;</span></strong> </dd>

            <dt class="reached" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($reached) ?>%"><span><?php echo Text::get('project-view-metter-got'); ?></span></dt>
            <dd class="reached" style="<?php echo $horizontal ? 'width' : 'height' ?>: <?php echo number_format($reached) ?>%"><strong><?php echo number_format($reached) ?> <span class="euro">&euro;</span></strong></dd>

            <dt class="days"><span><?php echo Text::get('project-view-metter-days'); ?></span></dt>
            <dd class="days"><strong><?php echo number_format($days) ?></strong> <?php echo Text::get('regular-days'); ?></dd>

            <dt class="supporters"><span><?php echo Text::get('project-view-metter-investors'); ?></span></dt>
            <dd class="supporters"><strong><?php echo number_format($supporters) ?></strong></dd>                

        </dl>

    <?php
    // si en estado 3 ha alcanzado el optimo o segunda ronda, "aun puedes seguir aportando" junto al quedan tantos días
    if ($project->status == 3 && ($project->round == 2  || $project->amount >= $project->maxcost || ($project->round == 1  && $project->amount >= $project->mincost) )) : ?>
        <div class="keepiton"><?php echo Text::get('regular-keepiton') ?></div>
    <?php endif;?>

    </div> 