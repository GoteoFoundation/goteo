<?php
$bodyClass = 'dashboard';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>

        <div id="main">

        <p><?php echo $this['message']; ?> [<a href="/logout">Salir</a>]</p>

		<p>
			Modificar Perfil (usuario/email/contraseña/imagen/descripción/información): <a href="/user/edit">Gestionar perfil</a><br />
			Perfil público (cómo me veo|cómo me ven): <a href="/user/<?php echo $_SESSION['user']->id ?>">Perfil público</a><br />
		</p>


		<p>
			Mis proyectos:<br />
		<?php
		foreach ($this['projects'] as $project) {
			echo '<a href="/project/' . $project->id . '/?edit">' . ($project->name != '' ? $project->name : $project->id) . '</a>
                (' . $this['status'][$project->status] . ')
                    Progreso: ' . $project->progress . '%
                        <a href="/project/' . $project->id . '">[Preview]</a><br />';
		}
		?>
		</p>

		<p>
			Nuevo proyecto: <a href="/project/?create">Crear</a><br />
		</p>

        </div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';