<?php

use Goteo\Core\View;

$level = (int) $this['level'] ?: 3;

$project = $this['project'];

?>
<div class="widget project-support collapsable" id="project-support">

    
    <h<?php echo $level + 1 ?> class="supertitle">Necesidades económicas</h<?php echo $level + 1 ?>>
    
    <?php echo new View('view/project/meter.html.php', array('project' => $project, 'level' => $level) ) ?>
    
    <?php if (in_array($project->status, array(4, 5))) : // tag de financiado, en estados financiado o retorno cumplido ?>
    <div>¡FINANCIADO!</div>
    <?php endif; ?>

    <div class="buttons">
        <?php if ($project->status == 3) : // boton apoyar solo si esta en campaña ?>
        <a class="button red supportit" href="/project/<?php echo $project->id; ?>/invest">Apóyalo</a>
        <?php endif; ?>
        <a class="more" href="/project/<?php echo $project->id; ?>/needs">Ver más</a>
    </div>
    
</div>