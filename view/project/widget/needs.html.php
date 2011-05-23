<?php

$project = $this['project'];
$types   = $this['types'];
$level = (int) $this['level'] ?: 3;

$minimum    = $project->mincost;
$optimum    = $project->maxcost;

// separar los costes por tipo
$costs = array();

foreach ($project->costs as $cost) {
    
    $costs[$cost->type][] = (object) array(
        'name' => $cost->cost,
        'description' => $cost->description,
        'min' => $cost->required == 1 ? $cost->amount : '',
        'opt' => $cost->required == 1 ? '' : $cost->amount
    );
}


?>
<div class="widget project-summary">
    
    <h<?php echo $level ?>>Financiación</h<?php echo $level ?>>

    <div id="project-costs-header">
        <span class="minimum" style="color:red;">Mínimo</span>
        <span class="optimum" style="color:black;">Óptimo</span>
    </div>

    <?php foreach ($costs as $type=>$list) : ?>
    <div class="<?php echo $type; ?>">
        <h<?php echo $level + 1?>><?php echo $types[$type]; ?></h<?php echo $level + 1?>>
        <?php foreach ($list as $cost) : ?>
            <strong><?php echo $cost->name; ?></strong>
            <span class="minimum" style="color:red;"><?php echo $cost->min; ?></span>
            <span class="optimum" style="color:black;"><?php echo $cost->opt; ?></span>
            <blockquote><?php echo $cost->description; ?></blockquote>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
    
    <div id="project-costs-footer">
        <span class="total">Total:</span>
        <span class="minimum" style="color:red;"><?php echo $minimum; ?></span>
        <span class="optimum" style="color:black;"><?php echo $optimum; ?></span>
    </div>
    
</div>