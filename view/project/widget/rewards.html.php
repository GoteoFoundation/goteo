<?php

$level = (int) $this['level'] ?: 3;

$project = $this['project'];

?>
<div class="widget project-support collapsable" id="project-rewards">
    
    <h<?php echo $level ?> class="title">Que ofrezco a cambio?</h<?php echo $level ?>>
    
    <h<?php echo $level+1 ?>>Retorno colectivo</h<?php echo $level+1 ?>>

    <div id="rewards-social">
        <?php foreach ($project->social_rewards as $social) : ?>
            <div class="<?php echo $social->icon; ?>">
                <blockquote><strong><?php echo $social->reward; ?></strong><br />
                <?php echo $social->description; ?></blockquote>
            </div>
        <?php endforeach; ?>
    </div>

    <h<?php echo $level+1 ?>>Recompensas individuales</h<?php echo $level+1 ?>>
    
    <div id="rewards-individual">
        <?php foreach ($project->individual_rewards as $individual) : ?>
        <div class="<?php echo $individual->icon; ?>">
                <div>Aportando: <span><?php echo $individual->amount; ?></span></div>
                <blockquote><strong><?php echo $individual->reward; ?></strong><br />
                <?php echo $individual->description; ?></blockquote>
                <?php if (!empty($individual->units)) : ?>
                    <strong>Recompensa limitada:</strong><br />
                    Quedan <span><?php echo ($individual->units - $individual->taken); ?></span> unidades
                <?php endif; ?>
                <div><span><?php echo $individual->taken; ?></span>Cofinanciadores</div>
            </div>
        <?php endforeach; ?>
    </div>

    
</div>