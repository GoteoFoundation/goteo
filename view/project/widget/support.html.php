<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$level = (int) $this['level'] ?: 3;

$project = $this['project'];

?>
<div class="widget project-support collapsable" id="project-support">

    
    <h<?php echo $level + 1 ?> class="supertitle"><?php echo Text::get('project-support-supertitle'); ?></h<?php echo $level + 1 ?>>
    
    <?php echo new View('view/project/meter.html.php', array('project' => $project, 'level' => $level) ) ?>
    
    <?php if (in_array($project->status, array(4, 5))) : // tag de financiado, en estados financiado o retorno cumplido ?>
    <div><?php echo Text::get('regular-success_mark'); ?></div>
    <?php endif; ?>

    <div class="buttons">
        <?php if ($project->status == 3) : // boton apoyar solo si esta en campaña ?>
        <a class="button red supportit" href="/project/<?php echo $project->id; ?>/invest"><?php echo Text::get('regular-invest_it'); ?></a>
        <?php endif; ?>
        <a class="more" href="/project/<?php echo $project->id; ?>/needs"><?php echo Text::get('regular-see_more'); ?></a>
    </div>
    
</div>