<?php
use Goteo\Core\View;

echo new View ('view/dashboard/projects/selector.html.php', $this);
?>
		<p>
			Mis proyectos:<br />
		<?php
		foreach ($this['projects'] as $project) {
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
		?>
		</p>



