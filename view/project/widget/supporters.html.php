<?php

$project = $this['project'];
$worthcracy = $this['worthcracy'];
$level = (int) $this['level'] ?: 3;

$reached    = $project->invested;
$supporters = count($project->investors);

?>
<div class="widget project-summary">
    
    <h<?php echo $level ?>>Cofinanciadores <?php echo $supporters; ?></h<?php echo $level ?>>
    Total de aportaciones <span><?php echo number_format($reached); ?> &euro;</span>
        
    <div id="project-supporters">
        <?php foreach ($project->investors as $investor) : ?>
            <div style="display:block;margin: 20px;">
                <img src="/image/<?php echo $investor->avatar->id; ?>/50/50" />
                <?php echo $investor->name; ?><br />
                    Cofinancia: <?php echo $investor->projects; ?> proyectos<br />
                    <?php echo $worthcracy[$investor->worth]->name; ?><br />
                    Aporta: <span class="amount"><?php echo number_format($investor->amount); ?> &euro;</span><br />
                    <span class="date"><?php echo $investor->date; ?></span>
            </div>
        <?php endforeach; ?>
    </div>    
    
    <div id="worthcracy">
        <?php foreach ($worthcracy as $level=>$worth) : ?>
            <div class="level worth-<?php $level; ?>" style="width:100px;float:left;">
                <?php echo '+ de ' . $worth->amount; ?><br />
                <span><?php echo $worth->name; ?></span>
            </div>
        <?php endforeach; ?>
    </div>    
    
</div>