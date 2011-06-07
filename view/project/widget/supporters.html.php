<?php

use Goteo\Core\View,
    Goteo\Library\Worth;

$project = $this['project'];

$level = (int) $this['level'] ?: 3;

$reached    = number_format($project->invested);
$supporters = count($project->investors);

$worthcracy = Worth::getAll();

?>
<div class="widget project-supporters">
    
    <h<?php echo $level ?> class="title">Cofinanciadores</h<?php echo $level ?>>
    
    <dl class="summary">
        <dt class="supporters">Cofinanciadores</dt>
        <dd class="supporters"><?php echo $supporters ?></dd>
        
        <dt class="reached">Total de aportaciones</dt>
        <dd class="reached"><?php echo $reached ?> <span class="euro">&euro;</span></dd>
        
    </dl>   
        
    <div class="supporters">
        <ul>
        <?php foreach ($project->investors as $investor): ?>
            <li><?php echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy)) ?></li>
        <?php endforeach ?>
        </ul>            
    </div>    

    <?php echo new View('view/worth/base.html.php', array('worthcracy' => $worthcracy, 'type' => 'main')); ?>
    
</div>