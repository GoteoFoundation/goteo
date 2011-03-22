<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
PROYECTO / Costes<br />
GUÍA<br />
<?php include 'view/project/errors.html.php' ?>
FORMULARIO <br />
<form action="/project/tasks" method="post">
	<dl>
		<?php foreach ($project->tasks as $task) : ?>
			<div>
				<dt><label for="task<?php echo $task->num; ?>">Tarea</label></dt>
				<dd><input type="text" name="task<?php echo $task->num; ?>" value="<?php echo $task->text; ?>"/></dd>
			</div>
		<?php endforeach; ?>

	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>