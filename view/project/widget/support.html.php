<?php

$level = (int) $this['level'] ?: 3;

$project = $this['project'];

$minimum    = $project->mincost;
$optimum    = $project->maxcost;
$reached    = $project->invested;
$supporters = count($project->investors);
$days       = $project->days;


// PHP la pifia (y mucho) con los cálculos en coma flotante
if ($reached >= $minimum) {
    
    $minimum_done = 100;
    $minimum_left = 0;
    
} else {
    
    $minimum_done = min(100, round(($reached / $minimum) * 100));
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

?>
<div class="widget project-support collapsable" id="project-support">
    
    <h<?php echo $level ?> class="title">Aportaciones económicas</h<?php echo $level ?>>
    
    <div class="data">
    
        <div class="meter">            
            <div class="optimum">
                 <div class="left" style="height: <?php echo number_format($optimum_left) ?>%"></div>
                 <div class="done" style="height: <?php echo number_format($optimum_done) ?>%"></div>
            </div>
            <div class="minimum">
                <div class="left" style="height: <?php echo number_format($minimum_left) ?>%"><strong><?php echo number_format($minimum_left) ?>%</strong></div>
                <div class="done" style="height: <?php echo number_format($minimum_done) ?>%"><strong><?php echo number_format($minimum_done) ?>%</strong></div>
            </div>
        </div>

        <dl>
            <dt class="optimum">Óptimo</dt>
            <dd class="optimum"><strong><?php echo number_format($optimum) ?></strong> <span class="euro">&euro;</span></dd>

            <dt class="minimum">Mínimo</dt>
            <dd class="minimum"><strong><?php echo number_format($minimum) ?></strong> <span class="euro">&euro;</span></dd>

            <dt class="reached">Obtenido</dt>
            <dd class="reached"><strong><?php echo number_format($reached) ?></strong> <span class="euro">&euro;</span></dd>

            <dt class="days">Quedan</dt>
            <dd class="days"><strong><?php echo number_format($days) ?></strong> días</dd>

            <dt class="supporters">Cofinanciadores</dt>
            <dd class="supporters"><strong><?php echo number_format($supporters) ?></strong></dd>                

        </dl>
        
    </div>   
    
    <a class="button red supportit" href="/invest/<?php echo $project->id; ?>">Apóyalo</a>
    
</div>