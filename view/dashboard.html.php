<?php
$bodyClass = 'dashboard';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

        <div id="main">

        <p><?php echo $this['message']; ?> <a href="/user/logout">[Cerrar sesión]</a></p>

		<p>
			<a href="/user/edit">[Gestionar perfil]</a><br />
			<a href="/user/<?php echo $_SESSION['user']->id ?>" target="_blank">[Ver perfil]</a><br />
		</p>


		<p>
			Mis proyectos:<br />
		<?php
		foreach ($this['projects'] as $project) {
			echo '<a href="/project/' . $project->id . '" target="_blank">' . ($project->name != '' ? $project->name : $project->id) . '</a>
                (' . $this['status'][$project->status] . ')';
            if ($project->status == 1) {
                echo ' Progreso: ' . $project->progress . '%
                <a href="/project/edit/' . $project->id . '">[Editar]</a>
                <a href="/project/trash/' . $project->id . '" onclick="return confirm(\'Seguro que desea eliminar este proyecto?\')">[Borrar]</a>';
            }
            echo '<br />';
		}
		?>
		</p>

		<p>
			<a href="/project/create">Crear nuevo proyecto</a><br />
		</p>

		<p>
			Mis cofinanciadores:<br />
		<?php
		foreach ($this['investors'] as $user=>$investor) {
			echo "{$investor->avatar} {$investor->name} De nivel {$investor->worth}  Cofinancia {$investor->projects} proyectos  Me aporta: {$investor->amount} € <br />";
		}
		?>
		</p>

		<p>
			Compartiendo intereses:<br />
		<?php
		foreach ($this['shares'] as $share) {
			echo "{$share->name} - Tiene " . $share->projects ." proyectos - Ha hecho " . $share->invests ." aportes<br />";
		}
		?>
		</p>

        </div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';