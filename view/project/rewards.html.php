<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
PROYECTO / Retorno<br />
GUÍA<br />
<?php include 'view/project/errors.html.php' ?>
<hr />
<!-- esto está así muy a lo loco -->
<form action="/project/rewards" method="post">
	RETORNOS COLECTIVOS:
	<dl>
		<dt><label for="nsocial_reward">Nuevo retorno colectivo</label></dt>
		<dd><textarea id="nsocial_reward" name="nsocial_reward" cols="50" rows="5"></textarea></dd>
		<input type="button" id="new-social_reward" value="Nuevo" />

		<?php foreach ($project->social_rewards as $social) : ?>
			<div>
				<dt><label for="social_reward<?php echo $social->num; ?>">Descripción</label></dt>
				<dd><textarea name="social_reward<?php echo $social->num; ?>" cols="50" rows="5"><?php echo $social->text; ?></textarea></dd>
			</div>
		<?php endforeach; ?>

	</dl>
	RETORNOS INDIVIDUALES:
	<dl>
		<label>Cantidad solicitada (&euro;):<input type="text" name="nindividual_reward-amount" value="" /></label><br />
		<label>Unidades disponibles:<input type="text" name="nindividual_reward-units" value="" /></label><br />

		<dt><label for="nindividual_reward">Nuevo retorno individual</label></dt>
		<dd><textarea id="nindividual_reward" name="nindividual_reward" cols="50" rows="5"></textarea></dd>
		<input type="button" id="new-individual_reward" value="Nuevo" />

		<?php foreach ($project->individual_rewards as $individual) : ?>
			<div>
				<label>Cantidad solicitada (&euro;):<input type="text" name="individual_reward-amount<?php echo $individual->num; ?>" value="<?php echo $individual->amount; ?>" /></label>
				<label>Unidades disponibles:<input type="text" name="individual_reward-units<?php echo $individual->num; ?>" value="<?php echo $individual->units; ?>" /></label>

				<dt><label for="individual_reward<?php echo $individual->num; ?>">Descripción</label></dt>
				<dd><textarea name="individual_reward<?php echo $individual->num; ?>" cols="50" rows="5"><?php echo $individual->text; ?></textarea></dd>
			</div>
		<?php endforeach; ?>

	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>