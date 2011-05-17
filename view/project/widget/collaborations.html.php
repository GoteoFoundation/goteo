<?php

$level = (int) $this['level'] ?: 3;

$project = $this['project'];

?>
<div class="widget project-support collapsable" id="project-collaborations">
    
    <h<?php echo $level ?> class="title">Aportaciones no económicas</h<?php echo $level ?>>

    <h<?php echo $level+1 ?>>Se busca</h<?php echo $level+1 ?>>

    <?php foreach ($project->supports as $support) : ?>
        <div class="<?php echo $support->type; ?>">
        <blockquote><strong><?php echo $support->support; ?></strong><br />
        <?php echo $support->description; ?></blockquote>
        </div>
    <?php endforeach; ?>

    <a class="vermas" href="/project/<?php echo $project->id; ?>/messages">Ver más</a><br />
    <a class="button green messageit" href="/project/<?php echo $project->id; ?>/messages">Colabora</a>
    
</div>