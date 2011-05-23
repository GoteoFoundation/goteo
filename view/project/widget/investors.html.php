<?php

$project = $this['project'];
$worthcracy = $this['worthcracy'];

$level = (int) $this['level'] ?: 3;

$supporters = count($project->investors);

?>
<div class="widget project-support collapsable" id="project-collaborations">
    
    <h<?php echo $level ?> class="title">Ya han aportado (<?php echo $supporters; ?>)</h<?php echo $level ?>>

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

    <a class="vermas" href="/project/<?php echo $project->id; ?>/supporters">Ver más</a><br />

    <div id="worthcracy-side">
        <?php foreach ($worthcracy as $level=>$worth) : ?>
            <div class="level worth-<?php $level; ?>" style="float:left;">
                <?php echo '+ de ' . $worth->amount; ?><br />
                <span><?php echo $worth->name; ?></span>
            </div>
        <?php endforeach; ?>
        <div style="float:left;">&euro;</div>
    </div>
    
    
</div>