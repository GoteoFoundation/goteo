<?php

use Goteo\Core\View,
    Goteo\Library\Worth;

$project = $this['project'];

$level = (int) $this['level'] ?: 3;

$reached    = $project->invested;
$supporters = count($project->investors);

$worthcracy = Worth::getAll();

?>
<div class="widget project-summary">
    
    <h<?php echo $level ?>>Cofinanciadores <?php echo $supporters; ?></h<?php echo $level ?>>
    Total de aportaciones <span><?php echo number_format($reached); ?> &euro;</span>
        
    <div id="project-supporters">
        <?php foreach ($project->investors as $investor) :
            echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy));
        endforeach; ?>
    </div>    

    <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'type' => 'main')); ?>
    
</div>