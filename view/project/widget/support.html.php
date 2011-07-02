<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$level = (int) $this['level'] ?: 3;

$project = $this['project'];

?>
<div class="widget project-support collapsable" id="project-support">

    <h<?php echo $level + 1 ?> class="supertitle"><?php echo Text::get('project-support-supertitle'); ?></h<?php echo $level + 1 ?>>
    
    <?php
 // tag de financiado cuando ha alcanzado el optimo o despues de los 80 dias
    if ($project->status == 4 || ( $project->status == 3 && $project->amount >= $project->maxcost )) :
        echo '<div class="tagmark red">' . Text::get('regular-gotit_mark') . '</div>';
// tag de en marcha cuando está en la segunda ronda o si estando en la primera ha alcanzado el mínimo
    elseif ($project->status == 3 && ($project->round == 2 ||  ( $project->round == 1 && $project->amount >= $project->mincost ))) :
        echo '<div class="tagmark green">' . Text::get('regular-onrun_mark') . '</div>';
 // tag de exitoso cuando es retorno cumplido
    elseif ($project->status == 5) :
        echo '<div class="tagmark red">' . Text::get('regular-success_mark') . '</div>';
    endif;
    ?>

    <?php echo new View('view/project/meter.html.php', array('project' => $project, 'level' => $level) ) ?>
    
    <div class="buttons">
        <?php if ($project->status == 3) : // boton apoyar solo si esta en campaña ?>
        <a class="button red supportit" href="/project/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it'); ?></a>
        <?php else : ?>
        <a class="button view" href="/project/<?php echo $project->id ?>/updates"><?php echo Text::get('regular-see_blog'); ?></a>
        <?php endif; ?>
        <a class="more" href="/project/<?php echo $project->id; ?>/needs"><?php echo Text::get('regular-see_more'); ?></a>
    </div>
    
</div>