<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
PROYECTO / Costes<br />
GUÍA<br />
<?php include 'view/project/errors.html.php' ?>
<hr />
<form action="/project/costs" method="post">
	<dl>
		<dt><label for="ncost">Nuevo coste</label></dt>
		<dd><input type="text" name="ncost" value=""/></dd>
		<input type="button" id="new-cost" value="Nueva" />
		
		<?php foreach ($project->costs as $cost) : ?>
			<div>
				<dt><label for="cost<?php echo $cost->num; ?>">Descripción</label></dt>
				<dd><input type="text" name="cost<?php echo $cost->num; ?>" value="<?php echo $cost->text; ?>"/></dd>
			</div>
		<?php endforeach; ?>

		<div>Mínimo: <?php echo $project->mincost; ?> &euro; | Óptimo: <?php echo $project->maxcost; ?> &euro;</div>

		<dt><label for="resource">Cuenta con otros recursos?<br />Otras ayudas económicas o infraestructura</label></dt>
		<dd><textarea id="resource" name="resource" cols="50" rows="5"><?php echo $project->resource; ?></textarea></dd>

		<div>
			AGENDA<br />Tiempo de producción del proyecto
			<?php foreach ($project->costs as $cost) : ?>
				<span><?php echo $cost->text; ?></span>
			<?php endforeach; ?>
		</div>
		
	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>