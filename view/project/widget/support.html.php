<?php

use Goteo\Core\View;

$level = (int) $this['level'] ?: 3;

$project = $this['project'];

?>
<div class="widget project-support collapsable" id="project-support">

    
    <h<?php echo $level + 1 ?> class="supertitle">Aportaciones económicas</h<?php echo $level + 1 ?>>
    <h<?php echo $level ?> class="title">Financiación</h<?php echo $level ?>>
    
    <?php echo new View('view/project/meter.html.php', array('project' => $project) ) ?>          
    
    <a class="button red supportit" href="/project/<?php echo $project->id; ?>/invest">Apóyalo</a>
    
</div>