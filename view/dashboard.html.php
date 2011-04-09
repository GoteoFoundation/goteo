<?php 
$bodyClass = 'dashboard';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>
        
        <div id="main">

        <p><?php echo $this['message']; ?></p>

		<p>
			Cambiar usuario/email/contraseña: <a href="/user/edit">Editar datos</a><br />
			Cambiar imagen/descripción/información: <a href="/user/profile">Gestionar perfil</a><br />
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
			Nuevo proyecto: <a href="/project/create">Crear</a><br />
		</p>
                
        </div>
    
<?php include 'view/footer.html.php' ?>