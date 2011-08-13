<?php
use Goteo\Library\Text;

$project = $this['project'];
$types   = $this['types'];
$level = (int) $this['level'] ?: 3;

$minimum    = $project->mincost . ' &euro;';
$optimum    = $project->maxcost . ' &euro;';

// separar los costes por tipo
$costs = array();

foreach ($project->costs as $cost) {
    
    $costs[$cost->type][] = (object) array(
        'name' => $cost->cost,
        'description' => $cost->description,
        'min' => $cost->required == 1 ? $cost->amount . ' &euro;' : '',
        'opt' => $cost->amount . ' &euro;',
        'req' => $cost->required
    );
}


?>
<div class="widget project-needs">
        
    <h<?php echo $level+1 ?> class="title"><?php echo Text::get('project-view-metter-investment'); ?></h<?php echo $level+1 ?>>
           
    <table width="100%">
        
        <?php foreach ($costs as $type => $list): ?>
        
        <thead class="<?php echo htmlspecialchars($type)?>">
            <tr>
                <th class="summary"><?php echo htmlspecialchars($types[$type]) ?></th>
                <th class="min"><?php echo Text::get('project-view-metter-minimum'); ?></th>
                <th class="max"><?php echo Text::get('project-view-metter-optimum'); ?></th>
            </tr>            
        </thead>
        
        <tbody>            
            <?php foreach ($list as $cost): ?>
            <tr<?php echo ($cost->req == 1) ? ' class="req"' : ' class="noreq"' ?>>
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
                <th class="total"><?php echo Text::get('regular-total'); ?></th>
                <th class="min"><?php echo $minimum ?></th>
                <th class="max"><?php echo $optimum ?></th>
            </tr>
        </tfoot>
        
    </table>
    
</div>