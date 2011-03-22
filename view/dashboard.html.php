<?php include 'view/header.html.php' ?>


        <p><?php echo $message; ?></p>

		<p>
			Cambiar usuario/email/contraseña: <a href="/user/edit">Editar datos</a><br />
			Cambiar imagen/descripción/información: <a href="/user/profile">Gestionar perfil</a><br />
		</p>


		<p>
			Mis proyectos:<br />
		<?php
		foreach ($projects as $proj) {
			echo '<a href="/project/manage/' . $proj->id . '">' . $proj->name . '</a><br />';
		}
		?>
		</p>

		<p>
			Nuevo proyecto: <a href="/project/create">Crear</a><br />
		</p>
    
<?php include 'view/footer.html.php' ?>