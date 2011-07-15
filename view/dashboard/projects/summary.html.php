<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Project;

echo new View ('view/dashboard/projects/selector.html.php', $this);

$project = $this['project'];

if (!$project instanceof  Goteo\Model\Project) {
    return;
}
?>
<div class="widget">
    <?php if ($project->name == '') : ?><p><strong>Ojo! Proyecto sin nombre</strong></p><?php endif ?>
    <a class="button" href="/project/edit/<?php echo $project->id ?>">Editar</a>
    <a class="button" href="/project/<?php echo $project->id ?>" target="_blank">Ver página pública</a>
    <?php if ($project->status == 1) : ?>
        <a class="button red" href="/project/delete/<?php echo $project->id ?>" onclick="return confirm('¿Seguro que deseas eliminar absoluta y definitivamente este proyecto?')">Eliminar</a>
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

<div id="project-support" class="widget project-support collapsable">

    <?php echo new View('view/project/meter.html.php', $this) ?>
    
</div>

