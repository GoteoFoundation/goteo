<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
PROYECTO / Costes<br />
GUÍA<br />
<?php include 'view/project/errors.html.php' ?>
<hr />
<form action="/project/costs" method="post">
	<dl>
		<dt><label for="ncost">Descripción</label></dt>
		<dd><input type="text" id="ncost" name="ncost" value=""/></dd>
		<dt><label for="ncost-amount">Costes</label></dt>
		<dd><input type="text" id="ncost-amount" name="ncost-amount" value="" /></dd>
		<dt><label for="ncost-type">Tipo</label></dt>
		<dd><select id="ncost-type" name="ncost-type">
				<option value="task">Tarea</option>
				<option value="structure">Infraestructuras</option>
				<option value="equip">Equipo</option>
			</select></dd>
		<dt><label for="ncost-required">Imprescindible</label></dt>
		<dd><input type="checkbox" id="ncost-required" name="ncost-required" value="1" /></dd>
		<dt><label for="ncost-from">Desde</label></dt>
		<dd><input type="text" id="ncost-from" name="ncost-from" value="<?php echo date('Y-m-d'); ?>" /></dd>
		<dt><label for="ncost-until">Hasta</label></dt>
		<dd><input type="text" id="ncost-until" name="ncost-until" value="<?php echo date('Y-m-d'); ?>" /></dd>
		<input type="button" id="new-cost" value="Nueva" />
		
		<?php foreach ($project->costs as $cost) : ?>
			<div>
				<dt><label for="cost<?php echo $cost->id; ?>">Descripción</label></dt>
				<dd><input type="text" id="cost<?php echo $cost->id; ?>" name="cost<?php echo $cost->id; ?>" value="<?php echo $cost->cost; ?>"/></dd>
				<dt><label for="cost-type<?php echo $cost->id; ?>">Tipo</label></dt>
				<dd><select id="cost-type<?php echo $cost->id; ?>" name="cost-type<?php echo $cost->id; ?>">
						<option value="task" <?php if ($cost->type=='task') echo ' selected="selected"'; ?>>Tarea</option>
						<option value="structure" <?php if ($cost->type=='structure') echo ' selected="selected"'; ?>>Infraestructuras</option>
						<option value="equip" <?php if ($cost->type=='equip') echo ' selected="selected"'; ?>>Equipo</option>
					</select></dd>
				<dt><label for="cost-amount<?php echo $cost->id; ?>">Costes</label></dt>
				<dd><input type="text" id="cost-amount<?php echo $cost->id; ?>" name="cost-amount<?php echo $cost->id; ?>" value="<?php echo $cost->amount; ?>" /></dd>
				<dt><label for="cost-required<?php echo $cost->id; ?>">Imprescindible</label></dt>
				<dd><input type="checkbox" id="cost-required<?php echo $cost->id; ?>" name="cost-required<?php echo $cost->id; ?>" value="1" <?php if ($cost->required) echo 'checked="checked"'; ?>/></dd>
				<dt><label for="cost-from<?php echo $cost->id; ?>">Desde</label></dt>
				<dd><input type="text" id="cost-from<?php echo $cost->id; ?>" name="cost-from<?php echo $cost->id; ?>" value="<?php echo $cost->from; ?>" /></dd>
				<dt><label for="cost-until<?php echo $cost->id; ?>">Hasta</label></dt>
				<dd><input type="text" id="cost-until<?php echo $cost->id; ?>" name="cost-until<?php echo $cost->id; ?>" value="<?php echo $cost->until; ?>" /></dd>
			</div>
		<?php endforeach; ?>

		<div id="total-costs">Mínimo: <?php echo $project->mincost; ?> &euro; | Óptimo: <?php echo $project->maxcost; ?> &euro;</div>

		<dt><label for="resource">Cuenta con otros recursos?<br />Otras ayudas económicas o infraestructura</label></dt>
		<dd><textarea id="resource" name="resource" cols="50" rows="5"><?php echo $project->resource; ?></textarea></dd>

		<div>
			AGENDA<br />Tiempo de producción del proyecto
			<?php foreach ($project->costs as $cost) : ?>
				<span><?php echo $cost->name; ?></span>
			<?php endforeach; ?>
		</div>
		
	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>