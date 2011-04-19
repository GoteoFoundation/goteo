<?php

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

?>
<div class="widget project-summary">
    
    <h<?php echo $level ?>><?php echo htmlspecialchars($project->name) ?></h<?php echo $level ?>>
        
    <?php if (!empty($project->description)): ?>
    <div class="description">
        <h<?php echo $level + 1?>>Descripción</h<?php echo $level + 1?>>        
        <?php echo htmlspecialchars($project->description) ?>
    </div>    
    <?php endif ?>
    
    <?php if (!empty($project->motivation)): ?>
    <div class="motivation">
        <h<?php echo $level + 1?>>Motivaciones</h<?php echo $level + 1?>>        
        <?php echo htmlspecialchars($project->motivation) ?>
    </div>    
    <?php endif ?>
    
    <?php if (!empty($project->goals)): ?>
    <div class="goal">
        <h<?php echo $level + 1?>>Objetivos</h<?php echo $level + 1?>>        
        <?php echo htmlspecialchars($project->goal) ?>
    </div>    
    <?php endif ?>
    
    
</div>