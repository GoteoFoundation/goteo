<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
PROYECTO / Colaboraciones<br />
GUÍA<br />
<?php include 'view/project/errors.html.php' ?>
<hr />
<form action="/project/supports" method="post">
	<dl>
		<div>
			<dt><label for="nsupport">Nueva colaboración</label></dt>
			<dd><input type="text" name="nsupport" value=""/></dd>

			<dt><label for="nsupport-type">Tipo</label></dt>
			<dd><select name="nsupport-type">
					<option value="task">Tarea</option>
					<option value="lend">Préstamo</option>
				</select></dd>

			<dt><label for="nsupport-description">Descripción</label></dt>
			<dd><textarea name="nsupport-description" cols="50" rows="5"></textarea></dd>
		</div>

		<?php foreach ($project->supports as $support) : ?>
			<div>
				<dt><label for="support<?php echo $support->id; ?>">Colaboración</label></dt>
				<dd><input type="text" name="support<?php echo $support->id; ?>" value="<?php echo $support->support; ?>"/></dd>

				<dt><label for="support-type<?php echo $support->id; ?>">Tipo</label></dt>
				<dd><select name="support-type<?php echo $support->id; ?>">
						<option value="task" <?php if ($support->type=='task') echo ' selected="selected"'; ?>>Tarea</option>
						<option value="lend" <?php if ($support->type=='lend') echo ' selected="selected"'; ?>>Préstamo</option>
					</select></dd>

				<dt><label for="support-description<?php echo $support->id; ?>">Descripción</label></dt>
				<dd><textarea name="support-description<?php echo $support->id; ?>" cols="50" rows="5"><?php echo $support->description; ?></textarea></dd>
			</div>
		<?php endforeach; ?>

	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>