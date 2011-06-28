<?php 

use Goteo\Core\View,
    Goteo\Library\Text;

$project = $this['project'];
$level = $this['level'] ?: 3;

?>

<div class="widget project">
    
    <?php if (isset($this['balloon'])): ?>
    <div class="balloon"><?php echo $this['balloon'] ?></div>
    <?php endif ?>

    <div class="image">
        <?php if (!empty($project->gallery)): ?>
        <a href="/project/<?php echo $project->id ?>"><img alt="<?php echo $project->name ?>" src="<?php echo htmlspecialchars(current($project->gallery)->getLink(255, 143)) ?>" /></a>
        <?php endif ?>
    </div>

    <h<?php echo $level ?> class="title"><a href="/project/<?php echo $project->id ?>" class="title"><?php echo htmlspecialchars($project->name) ?></a></h<?php echo $level ?>>
    
    <h<?php echo $level + 1 ?> class="author">Por: <a href="/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"><?php echo htmlspecialchars($project->user->name) ?></a></h<?php echo $level + 1?>>
    
    <?php if (in_array($project->status, array(4, 5))) : // en estados financiado o retorno cumplido, tag de financiado ?>
    <div><?php echo Text::get('regular-success_mark'); ?></div>
    <?php endif; ?>

    <div class="description"><?php echo Text::recorta($project->description, 100); ?></div>

    <?php echo new View('view/project/meter_hor.html.php', array('project' => $project)) ?>
    
    <div class="rewards">
        <h<?php echo $level + 1 ?>><?php echo Text::get('project-rewards-header'); ?></h<?php echo $level + 1?>>
        
        <ul>
           <?php $q = 1; foreach ($project->social_rewards as $social): ?>
            <li class="<?php echo $social->icon ?>">
                <a href="/project/<?php echo $project->id ?>/rewards" title="<?php echo htmlspecialchars("{$social->reward} al procomún") ?>" class="tipsy"><?php echo htmlspecialchars($social->reward) ?></a>
            </li>
           <?php if ($q > 5) break; $q++; 
               endforeach ?>
           <?php if ($q < 5) foreach ($project->individual_rewards as $individual): ?>
            <li class="<?php echo $individual->icon ?>">
                <a href="/project/<?php echo $project->id ?>/rewards" title="<?php echo htmlspecialchars("{$individual->reward} aportando {$individual->amount}") ?> &euro;" class="tipsy"><?php echo htmlspecialchars($individual->reward) ?></a>
            </li>
           <?php if ($q > 5) break; $q++;
           endforeach ?>
        </ul>
        
        
    </div>

    <?php if ($this['dashboard'] === true) : // si estamos en el dashboard no hay (apoyar y el ver se abre en una ventana nueva) ?>
    <div class="buttons">
        <?php if ($this['own'] === true) : // si es propio puede ir a editarlo ?>
        <a class="button" href="/project/edit/<?php echo $project->id ?>"><?php echo Text::get('regular-edit'); ?></a>
        <?php endif; ?>
        <a class="button view" href="/project/<?php echo $project->id ?>" target="_blank"><?php echo Text::get('regular-view_project'); ?></a>
    </div>
    <?php else : // normal ?>
    <div class="buttons">
        <a class="button red supportit" href="/invest/<?php echo $project->id ?>"><?php echo Text::get('regular-invest_it'); ?></a>
        <a class="button view" href="/project/<?php echo $project->id ?>"><?php echo Text::get('regular-view_project'); ?></a>
    </div>
    <?php endif; ?>


</div>