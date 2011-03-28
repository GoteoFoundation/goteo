<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
<?php use Goteo\Library\Text; ?>
PROYECTO / Retorno<br />
GUÍA: <?php echo $guideText;  ?><br />
<?php include 'view/project/errors.html.php' ?>
<hr />
<!-- esto está así muy a lo loco -->
<form action="/project/rewards" method="post">
	RETORNOS COLECTIVOS:
	<dl>
		<dt><label for="nsocial_reward">Nuevo retorno colectivo</label></dt>
		<dd><textarea id="nsocial_reward" name="nsocial_reward" cols="50" rows="5"></textarea></dd>
		<input type="button" id="new-social_reward" value="Nuevo" />
		<span><?php echo Text::get('tooltip project nsocial_reward'); ?></span><br />

		<?php foreach ($project->social_rewards as $social) : ?>
			<div>
				<dt><label for="social_reward<?php echo $social->id; ?>">Descripción</label></dt>
				<dd><textarea id="social_reward<?php echo $social->id; ?>" name="social_reward<?php echo $social->id; ?>" cols="50" rows="5"><?php echo $social->reward; ?></textarea></dd>
				<span><?php echo Text::get('tooltip project social_reward'); ?></span><br />
			</div>
		<?php endforeach; ?>

	</dl>
	RETORNOS INDIVIDUALES:
	<dl>
		<label>Cantidad solicitada (&euro;):<input type="text" id="nindividual_reward-amount" name="nindividual_reward-amount" value="" /></label><br />
		<label>Unidades disponibles:<input type="text" id="nindividual_reward-units" name="nindividual_reward-units" value="" /></label><br />

		<dt><label for="nindividual_reward">Nuevo retorno individual</label></dt>
		<dd><textarea id="nindividual_reward" name="nindividual_reward" cols="50" rows="5"></textarea></dd>
		<input type="button" id="new-individual_reward" value="Nuevo" />
		<span><?php echo Text::get('tooltip project nindividual_reward'); ?></span><br />

		<?php foreach ($project->individual_rewards as $individual) : ?>
			<div>
				<label>Cantidad solicitada (&euro;):<input type="text" id="individual_reward-amount<?php echo $individual->id; ?>" name="individual_reward-amount<?php echo $individual->id; ?>" value="<?php echo $individual->amount; ?>" /></label>
				<label>Unidades disponibles:<input type="text" id="individual_reward-units<?php echo $individual->id; ?>" name="individual_reward-units<?php echo $individual->id; ?>" value="<?php echo $individual->units; ?>" /></label>

				<dt><label for="individual_reward<?php echo $individual->id; ?>">Descripción</label></dt>
				<dd><textarea id="individual_reward<?php echo $individual->id; ?>" name="individual_reward<?php echo $individual->id; ?>" cols="50" rows="5"><?php echo $individual->reward; ?></textarea></dd>
				<span><?php echo Text::get('tooltip project individual_reward'); ?></span><br />
			</div>
		<?php endforeach; ?>

	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>