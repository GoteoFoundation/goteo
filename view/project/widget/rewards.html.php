<?php

$level = (int) $this['level'] ?: 3;

$project = $this['project'];

?>
<div class="widget project-rewards collapsable" id="project-rewards">
    
    <h<?php echo $level + 1 ?> class="supertitle">Que ofrezco a cambio?</h<?php echo $level + 1?>>
       
    <div class="social">
        <h<?php echo $level ?> class="title">Retorno colectivo</h<?php echo $level ?>>
        <ul>
        <?php foreach ($project->social_rewards as $social) : ?>
            <li class="<?php echo $social->icon ?>">                
                <strong><?php echo htmlspecialchars($social->reward) ?></strong>
                <p><?php echo htmlspecialchars($social->description)?></p>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
        
    <div class="individual">
        <h<?php echo $level+1 ?> class="title">Recompensas individuales</h<?php echo $level+1 ?>>
        <ul>
        <?php foreach ($project->individual_rewards as $individual) : ?>
        <li class="<?php echo $individual->icon ?>">
            
            <div>Aportando: <span><?php echo $individual->amount; ?>&euro;</span></div>
            <strong><?php echo htmlspecialchars($individual->reward) ?></strong>
            <p><?php echo htmlspecialchars($individual->description) ?></p>

            <?php if (!empty($individual->units)) : ?>
                <div>
                    <strong>Recompensa limitada:</strong><br />
                    Quedan <span><?php echo ($individual->units - $individual->taken); ?></span> unidades
                </div>
            <?php endif; ?>

                <div><span><?php echo (int) $individual->taken; ?></span> Cofinanciadores</div>

        </li>
        <?php endforeach ?>
    </div>
    
    <a class="more" href="/project/<?php echo $project->id; ?>/rewards">Ver más</a>

    
</div>