<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Project\Category,
    Goteo\Model\Invest,
    Goteo\Model\Image;

$project = $this['project'];
$level = $this['level'] ?: 3;

$categories = Category::getNames($project->id, 2);

//si llega $this['investor'] sacamos el total aportado para poner en "mi aporte"
if (isset($this['investor']) && is_object($this['investor'])) {
    $investor = $this['investor'];
    $invest = Invest::supported($investor->id, $project->id);
}
?>

<div class="widget project activable<?php if (isset($this['balloon'])) echo ' balloon' ?>">

    <?php if (isset($this['balloon'])): ?>
    <div class="balloon"><?php echo $this['balloon'] ?></div>
    <?php endif ?>

    <div class="image">
        <?php
     // tag de financiado cuando ha alcanzado el optimo o despues de los 80 dias
        if ($project->status == 4 || ( $project->status == 3 && $project->amount >= $project->maxcost )) :
            echo '<div class="tagmark red">' . Text::get('regular-gotit_mark') . '</div>';
    // tag de en marcha cuando está en la segunda ronda o si estando en la primera ha alcanzado el mínimo
        elseif ($project->status == 3 && ($project->round == 2 ||  ( $project->round == 1 && $project->amount >= $project->mincost ))) :
            echo '<div class="tagmark green">' . Text::get('regular-onrun_mark') . '</div>';
     // tag de exitoso cuando es retorno cumplido
        elseif ($project->status == 5) :
            echo '<div class="tagmark red">' . Text::get('regular-success_mark') . '</div>';
        endif;
        ?>

        <?php if (isset($this['investor'])) : ?>
            <div class="investor"><img src="/image/<?php echo $investor->avatar->id ?>/43/43/1" alt="<?php echo $investor->name ?>" /><div class="invest">Mi aporte<br /><span class="amount"><?php echo $invest->total ?></span></div></div>
        <?php endif; ?>

        <?php if (!empty($project->gallery) && (current($project->gallery) instanceof Image)): ?>
        <a href="/project/<?php echo $project->id ?>"><img alt="<?php echo $project->name ?>" src="<?php echo htmlspecialchars(current($project->gallery)->getLink(255, 130)) ?>" /></a>
        <?php endif ?>
        <?php if (!empty($categories)): ?>
        <div class="categories">
        <?php $sep = ''; foreach ($categories as $key=>$value) :
            echo $sep.htmlspecialchars($value);
        $sep = ', '; endforeach; ?>
        </div>
        <?php endif ?>
    </div>

    <h<?php echo $level ?> class="title"><a href="/project/<?php echo $project->id ?>"><?php echo htmlspecialchars($project->name) ?></a></h<?php echo $level ?>>

    <h<?php echo $level + 1 ?> class="author">Por: <a href="/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"><?php echo htmlspecialchars($project->user->name) ?></a></h<?php echo $level + 1?>>

    <div class="description"><?php echo Text::recorta($project->description, 100); ?></div>

    <?php echo new View('view/project/meter_hor.html.php', array('project' => $project)) ?>

    <div class="rewards">
        <h<?php echo $level + 1 ?>><?php echo Text::get('project-rewards-header'); ?></h<?php echo $level + 1?>>

        <ul>
           <?php $q = 1; foreach ($project->social_rewards as $social): ?>
            <li class="<?php echo $social->icon ?> activable">
                <a href="/project/<?php echo $project->id ?>/rewards" title="<?php echo htmlspecialchars("{$social->icon_name}: {$social->reward} al procomún") ?>" class="tipsy"><?php echo htmlspecialchars($social->reward) ?></a>
            </li>
           <?php if ($q > 5) break; $q++;
               endforeach ?>
           <?php if ($q < 5) foreach ($project->individual_rewards as $individual): ?>
            <li class="<?php echo $individual->icon ?> activable">
                <a href="/project/<?php echo $project->id ?>/rewards" title="<?php echo htmlspecialchars("{$individual->icon_name}: {$individual->reward} aportando {$individual->amount}") ?> &euro;" class="tipsy"><?php echo htmlspecialchars($individual->reward) ?></a>
            </li>
           <?php if ($q > 5) break; $q++;
           endforeach ?>
        </ul>


    </div>

    <?php if ($this['dashboard'] === true) : // si estamos en el dashboard no hay (apoyar y el ver se abre en una ventana nueva) ?>
    <div class="buttons">
        <?php if ($this['own'] === true) : // si es propio puede ir a editarlo ?>
        <a class="button red suportit" href="/project/edit/<?php echo $project->id ?>"><?php echo Text::get('regular-edit'); ?></a>
        <?php endif; ?>
        <a class="button view" href="/project/<?php echo $project->id ?>" target="_blank"><?php echo Text::get('regular-view_project'); ?></a>
    </div>
    <?php else : // normal ?>
    <div class="buttons">
        <?php if ($project->status == 3) : // si esta en campaña se puede aportar ?>
        <a class="button violet supportit" href="/invest/<?php echo $project->id ?>"><?php echo Text::get('regular-invest_it'); ?></a>
        <?php else : ?>
        <a class="button view" href="/project/<?php echo $project->id ?>/updates"><?php echo Text::get('regular-see_blog'); ?></a>
        <?php endif; ?>
        <a class="button view" href="/project/<?php echo $project->id ?>"><?php echo Text::get('regular-view_project'); ?></a>
    </div>
    <?php endif; ?>
</div>
