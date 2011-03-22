<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
PROYECTO / Retorno<br />
GUÍA<br />
<?php include 'view/project/errors.html.php' ?>
FORMULARIO <br />
<form action="/project/rewards" method="post">
	<dl>
		<?php foreach ($project->rewards as $reward) : ?>
			<div>
				<dt><label for="reward<?php echo $reward->num; ?>">Retorno</label></dt>
				<dd><input type="text" name="reward<?php echo $reward->num; ?>" value="<?php echo $reward->text; ?>"/></dd>
			</div>
		<?php endforeach; ?>

	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>