<?php include 'view/header.html.php' ?>


        <p><?php echo $message; ?></p>

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