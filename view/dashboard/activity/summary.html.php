<?php
use Goteo\Core\View;
?>
<div class="widget projects">
    <h2 class="title">Mis Proyectos</h2>
    <?php foreach ($this['projects'] as $project) : ?>
        <div>
            <?php
            // es instancia del proyecto
            // se pintan con widget horizontal 
            // muestran el estado en vez del creador // own
            // muestran el boton editar si esta en edicion // own
            // no muestran el boton apoyar  // dashboard
            echo new View('view/project/widget/project.html.php', array(
                'project'   => $project,
                'dashboard' => true,
                'own'       => true
            )); ?>
        </div>
    <?php endforeach; ?>
</div>



            <?php
            /*
            if (in_array($project->status, array(3, 4, 5))) {
                echo '<a href="/project/' . $project->id . '" target="_blank">';
                echo $project->name;
                echo '</a>';
            } else {
                echo ($project->name != '' ? $project->name : 'Ojo! Proyecto sin nombre');
            }

                echo '(' . $this['status'][$project->status] . ')';
            if ($project->status == 1) {
                echo ' Progreso: ' . $project->progress . '%
                <a href="/project/edit/' . $project->id . '">[Editar]</a>
                <a href="/project/delete/' . $project->id . '" onclick="return confirm(\'Seguro que desea eliminar este proyecto?\')">[Borrar]</a>';
            }
            echo '<br />';
		}
             *
             */
		?>
