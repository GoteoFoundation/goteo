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
        'opt' => $cost->amount
    );
}


?>
<div class="widget project-needs">
        
    <!--
    <div id="project-costs-header">
        <span class="minimum" style="color:red;">Mínimo</span>
        <span class="optimum" style="color:black;">Óptimo</span>
    </div>
    -->
        
    
    <!--<h<?php echo $level ?>>Necesidades</h<?php echo $level ?>>-->
           
    <table>
        
        <?php foreach ($costs as $type => $list): ?>
        
        <thead class="<?php echo htmlspecialchars($type)?>">
            <tr>
                <th class="summary"><?php echo htmlspecialchars($types[$type]) ?></th>
                <th class="min">Mínimo</th>
                <th class="max">Óptimo</th>
            </tr>            
        </thead>
        
        <tbody>            
            <?php foreach ($list as $cost): ?>
            <tr>
                <th class="summary"><strong><?php echo htmlspecialchars($cost->name) ?></strong>
                <blockquote><?php echo $cost->description ?></blockquote>
                </th>
                <td class="min"><?php echo $cost->min ?></td>
                <td class="max"><?php echo $cost->opt ?></td>
            </tr>            
            <?php endforeach ?>
        </tbody>
        
        <?php endforeach ?>
                                        
        <tfoot>
            <tr>
                <th class="total">Total</th>
                <th class="min"><?php echo $minimum ?></th>
                <th class="max"><?php echo $optimum ?></th>
            </tr>
        </tfoot>
        
    </table>
    
</div>