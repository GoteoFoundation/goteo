<?php

$project = $this['project'];
$level = (int) $this['level'] ?: 3;

?>
<div class="widget project-summary">
    
    <h<?php echo $level ?>><?php echo htmlspecialchars($project->name) ?></h<?php echo $level ?>>
        
    <?php if (!empty($project->description)): ?>
    <div class="description">
<!--        <h<?php echo $level + 1?>>Descripción</h<?php echo $level + 1?>>         -->
        <?php echo htmlspecialchars($project->description) ?>
    </div>    
    <?php endif ?>

    <!-- Carrousel de imágenes  aquí -->
    <?php if (!empty($project->gallery)): ?>
    <div class="gallery">
        <?php foreach ($project->gallery as $image) : ?>
            <img src="/image/<?php echo $image->id; ?>/175/175" alt="<?php echo $project->name; ?>" />
        <?php endforeach; ?>
    </div>
    <?php endif ?>



    <?php if (!empty($project->about)): ?>
    <div class="about">
        <h<?php echo $level + 1?>>Que es</h<?php echo $level + 1?>>
        <?php echo htmlspecialchars($project->about) ?>
    </div>    
    <?php endif ?>
    
    <?php if (!empty($project->motivation)): ?>
    <div class="motivation">
        <h<?php echo $level + 1?>>Motivación</h<?php echo $level + 1?>>
        <?php echo htmlspecialchars($project->motivation) ?>
    </div>
    <?php endif ?>

    <?php if (!empty($project->goal)): ?>
    <div class="goal">
        <h<?php echo $level + 1?>>Objetivos</h<?php echo $level + 1?>>        
        <?php echo htmlspecialchars($project->goal) ?>
    </div>    
    <?php endif ?>
    
    <?php if (!empty($project->related)): ?>
    <div class="related">
        <h<?php echo $level + 1?>>Experiencia relacionada y equipo</h<?php echo $level + 1?>>
        <?php echo htmlspecialchars($project->related) ?>
    </div>
    <?php endif ?>

    
</div>