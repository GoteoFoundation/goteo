<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Project;

$project = $this['project'];

if (!$project instanceof  Goteo\Model\Project) {
    return;
}
?>
<div class="widget">
    <?php if ($project->name == '') : ?>
        <p><strong>Ojo! Proyecto sin nombre</strong></p>
    <?php else : ?>
        <p><strong><?php echo $project->name ?></strong></p>
    <?php endif ?>
        <a class="button red" href="/project/edit/<?php echo $project->id ?>"><?php echo Text::get('regular-edit') ?></a>
    <a class="button" href="/project/<?php echo $project->id ?>" target="_blank">Ver página pública</a>
    <?php if ($project->status == 1) : ?>
        <a class="button weak" href="/project/delete/<?php echo $project->id ?>" onclick="return confirm('¿Seguro que deseas eliminar absoluta y definitivamente este proyecto?')">Eliminar</a>
    <?php endif ?>
</div>

<div class="status">

    <div id="project-status">
        <h3><?php echo Text::get('form-project-status-title'); ?></h3>
        <ul>
            <?php foreach (Project::status() as $i => $s): ?>
            <li><?php if ($i == $project->status) echo '<strong>' ?><?php echo htmlspecialchars($s) ?><?php if ($i == $project->status) echo '</strong>' ?></li>
            <?php endforeach ?>
        </ul>
    </div>

</div>

<div id="meter-big" class="widget collapsable">

    <?php echo new View('view/project/meter_hor_big.html.php', $this) ?>
    
</div>

