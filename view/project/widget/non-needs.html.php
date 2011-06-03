<?php

$project = $this['project'];
$types   = $this['types'];
$level = (int) $this['level'] ?: 3;

$minimum    = $project->mincost;
$optimum    = $project->maxcost;

// separar los costes por tipo
$items = array();

foreach ($project->supports as $item) {
    
    $items[$item->type][] = (object) array(
        'name' => $item->support,
        'description' => $item->description
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
        
        <?php foreach ($items as $type => $list): ?>
        
        <thead class="<?php echo htmlspecialchars($type)?>">
            <tr>
                <th class="summary"><?php echo htmlspecialchars($types[$type]) ?></th>
            </tr>            
        </thead>
        
        <tbody>            
            <?php foreach ($list as $item): ?>
            <tr>
                <th class="summary"><strong><?php echo htmlspecialchars($item->name) ?></strong>
                <blockquote><?php echo $item->description ?></blockquote>
                </th>
            </tr>            
            <?php endforeach ?>
        </tbody>
        
        <?php endforeach ?>
                                        
    </table>
    
</div>