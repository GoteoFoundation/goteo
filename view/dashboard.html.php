<?php 
$bodyClass = 'dashboard';
include 'view/prologue.html.php';
include 'view/header.html.php'; ?>
        
        <div id="main">

        <p><?php echo $message; ?></p>

		<p>
			Cambiar usuario/email/contraseña: <a href="/user/edit">Editar datos</a><br />
			Cambiar imagen/descripción/información: <a href="/user/profile">Gestionar perfil</a><br />
		</p>


		<p>
			Mis proyectos:<br />
		<?php
		foreach ($projects as $proj) {
			echo '<a href="/project/manage/' . $proj->id . '">' . ($proj->name != '' ? $proj->name : $proj->id) . '</a><br />';
		}
		?>
		</p>

		<p>
			Nuevo proyecto: <a href="/project/create">Crear</a><br />
		</p>
                
        </div>
    
<?php include 'view/footer.html.php' ?>