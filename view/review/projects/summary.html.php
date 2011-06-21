<?php
use Goteo\Core\View;

echo new View ('view/review/projects/selector.html.php', $this);

$project = $this['project'];

$status = new View('view/project/edit/status.html.php', array('status' => $project->status, 'progress' => $project->progress));
$metter = new View('view/project/widget/support.html.php', array('project' => $project));
?>

<!-- operaciones (segun estado) editar, borrar  -->
<p>
    Operaciones:<br />
    <?php
    if ($project->name == '') echo '<strong>Ojo! Proyecto sin nombre</strong><br />';
    if (in_array($project->status, array(3, 4, 5))) {
        echo '<a href="/project/' . $project->id . '" target="_blank">Ver publicado</a><br />';
    }
    if ($project->status == 1) {
        echo '<a href="/project/edit/' . $project->id . '">[Editar]</a>';
        echo '<a href="/project/delete/' . $project->id . '" onclick="return confirm(\'Seguro que desea eliminar este proyecto?\')">[Borrar]</a>';
    }
    ?>
</p>

<!-- estado del proyecto de trabajo -->
<p>
    Estado: <?php echo $this['status'][$project->status]; ?><br />
    Progreso: <?php echo $project->progress . '%'; ?><br />
</p>
<?php echo $status; ?>
<!-- termometro (tendria que ser horizontal, cuando lo tengamos) -->
<?php echo $metter; ?>
