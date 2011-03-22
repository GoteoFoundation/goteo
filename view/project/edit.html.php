<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
PROYECTO / Descripción<br />
GUÍA<br />
<?php include 'view/project/errors.html.php' ?>
FORMULARIO <br />
<form action="/project/edit" method="post">
	<dl>
		<dt><label for="name">Nombre del proyecto</label></dt>
		<dd><input type="text" id="name" name="name" value="<?php echo $project->name; ?>"/></dd>

		<dt><label for="image">Imagen del proyecto</label></dt>
		<dd><input type="file" id="theimage" name="theimage" value=""/> img src="<?php echo $project->image; ?>" </dd>
		<input type="text" name="image" value="project.jpg" />

		<dt><label for="description">Descripción</label></dt>
		<dd><textarea id="description" name="description" cols="100" rows="10"><?php echo $project->description; ?></textarea></dd>

	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>