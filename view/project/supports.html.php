<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
PROYECTO / Colaboraciones<br />
GUÍA<br />
<?php include 'view/project/errors.html.php' ?>
FORMULARIO <br />
<form action="/project/supports" method="post">
	<dl>
		<?php foreach ($project->supports as $support) : ?>
			<div>
				<dt><label for="support<?php echo $support->num; ?>">Colaboración</label></dt>
				<dd><input type="text" name="support<?php echo $support->num; ?>" value="<?php echo $support->text; ?>"/></dd>
			</div>
		<?php endforeach; ?>

	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>