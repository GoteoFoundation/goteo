<?php 

use Goteo\Core\View;

$project = $this['project'];
$level = $this['level'] ?: 3;

?>

<div class="widget project">
    
    <?php if (isset($this['balloon'])): ?>
    <div class="balloon"><?php echo $this['balloon'] ?></div>
    <?php endif ?>
    
    <div class="image">
        <?php if (!empty($project->gallery)): ?>
        <img alt="" src="<?php echo htmlspecialchars(current($project->gallery)->getLink(255, 143)) ?>" />
        <?php endif ?>
    </div>

    <h<?php echo $level ?> class="title"><?php echo htmlspecialchars($project->name) ?></h<?php echo $level ?>>
    
    <h<?php echo $level + 1 ?> class="author">Por: <a href="/user/profile/<?php echo htmlspecialchars($project->user->id) ?>"><?php echo htmlspecialchars($project->user->name) ?></a></h<?php echo $level + 1?>>
    
    <div class="description"><?php echo $project->description ?></div>

    <?php echo new View('view/project/meter_hor.html.php', array('project' => $project)) ?>

    <div class="rewards-mini">
        <strong>Retorno</strong>
        <?php foreach ($project->individual_rewards as $individual) : ?>
            <a href="/project/<?php echo $project->id; ?>/rewards" title="<?php echo "{$individual->reward} aportando {$individual->amount} &euro; "; ?>"><?php echo $individual->icon ?></a>
        <?php endforeach; ?>
    </div>

    <div class="buttons">
        <a class="button red supportit" href="/invest/<?php echo $project->id ?>">Apóyalo</a>
        <a class="button view" href="/project/<?php echo $project->id ?>">Ver proyecto</a>
    </div>

</div>