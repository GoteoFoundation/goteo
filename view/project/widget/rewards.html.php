<?php

use Goteo\Model\License;

$level = (int) $this['level'] ?: 3;

$project = $this['project'];

$licenses = array();

foreach (License::getAll() as $l) {
    $licenses[$l->id] = $l;
}

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
                <?php if (!empty($social->license) && array_key_exists($social->license, $licenses)): ?>
                <div class="license" class="<?php echo htmlspecialchars($social->license) ?>">
                    <a href="<?php echo htmlspecialchars($licenses[$social->license]->url) ?>" target="_blank">
                        <strong><?php echo htmlspecialchars($licenses[$social->license]->name) ?></strong>
                        <?php if (!empty($licenses[$social->license]->description)): ?><br /> 
                        <p><?php echo htmlspecialchars($licenses[$social->license]->description) ?></p>
                        <?php endif ?>
                </div>
                <?php endif ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
        
    <div class="individual">
        <h<?php echo $level+1 ?> class="title">Recompensas individuales</h<?php echo $level+1 ?>>
        <ul>
        <?php foreach ($project->individual_rewards as $individual) : ?>
        <li class="<?php echo $individual->icon ?>">
            
            <!--  <div>Aportando: <span><?php echo $individual->amount; ?></span></div> -->
            <strong><?php echo htmlspecialchars($individual->reward) ?></strong>
            <p><?php echo htmlspecialchars($individual->description) ?></p>
                
            <!--
                    <?php if (!empty($individual->units)) : ?>
                    <strong>Recompensa limitada:</strong><br />
                    Quedan <span><?php echo ($individual->units - $individual->taken); ?></span> unidades
                <?php endif; ?>
                <div><span><?php echo $individual->taken; ?></span>Cofinanciadores</div>
            
            -->
        </li>
        <?php endforeach ?>
    </div>
    
    <a class="more" href="/project/<?php echo $project->id; ?>/rewards">Ver más</a>

    
</div>