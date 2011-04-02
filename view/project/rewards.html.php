<?php include 'view/project/header.html.php' ?>
<?php use Goteo\Library\Text; ?>
PROYECTO / Retorno<br />
GUÍA: <?php echo $guideText; ?><br />
<?php include 'view/project/errors.html.php' ?>
<hr />
<!-- esto está así muy a lo loco -->
<form action="/project/rewards" method="post">
	<fieldset><legend>Nuevo retorno colectivo</legend>
		<dl>
			<dt><label for="nsocial_reward">Retorno colectivo</label></dt>
			<dd><input type="text" id="nsocial_reward" name="nsocial_reward" value="" /></dd>

			<dt><label for="nsocial_reward-description">Descripción</label></dt>
			<dd><textarea id="nsocial_reward-description" name="nsocial_reward-description" cols="50" rows="5"></textarea></dd>

			<dt><label for="nsocial_reward-icon">Tipo</label></dt>
			<dd><select id="nsocial_reward-icon" name="nsocial_reward-icon">
<?php foreach ($stypes as $Id => $Val) : ?>
					<option value="<?php echo $Id; ?>"><?php echo $Val; ?></option>
<?php endforeach; ?>
				</select></dd>
		</dl>
		<input type="button" id="new-social_reward" value="Nuevo" />

		<span><?php echo Text::get('tooltip project nsocial_reward'); ?></span>
	</fieldset>

	<h3>Retornos colectivos</h3>
<?php foreach ($project->social_rewards as $social) : ?>
		<fieldset><legend>Retorno colectivo <?php echo $social->id; ?></legend>
			<label>REMOVE! <input type="checkbox" name="remove-social_reward<?php echo $social->id; ?>" value="1" /></label><br />
			<span>Tipo: <?php echo $stypes[$social->icon]; ?></span>
			<dl>
				<dt><label for="social_reward<?php echo $social->id; ?>">Retorno colectivo</label></dt>
				<dd><input type="text" id="social_reward<?php echo $social->id; ?>" name="social_reward<?php echo $social->id; ?>" value="<?php echo $social->reward; ?>" /></dd>

				<dt><label for="social_reward-description<?php echo $social->id; ?>">Descripción</label></dt>
				<dd><textarea id="social_reward-description<?php echo $social->id; ?>" name="social_reward-description<?php echo $social->id; ?>" cols="50" rows="5"><?php echo $social->description; ?></textarea></dd>
			</dl>

			<span><?php echo Text::get('tooltip project social_reward'); ?></span><br />
		</fieldset>
<?php endforeach; ?>




		<fieldset><legend>Nueva recompensa individual</legend>
			<label>Cantidad solicitada (&euro;):<input type="text" id="nindividual_reward-amount" name="nindividual_reward-amount" value="" /></label><br />
			<label>Unidades disponibles:<input type="text" id="nindividual_reward-units" name="nindividual_reward-units" value="" /></label><br />

			<dl>
				<dt><label for="nindividual_reward">Recompensa individual</label></dt>
				<dd><input type="text" id="nindividual_reward" name="nindividual_reward" value=""/></dd>

				<dt><label for="nindividual_reward-description">Descripción</label></dt>
				<dd><textarea id="nindividual_reward-description" name="nindividual_reward-description" cols="50" rows="5"></textarea></dd>

				<dt><label for="nindividual_reward-icon">Tipo</label></dt>
				<dd><select id="nindividual_reward-icon" name="nindividual_reward-icon">
<?php foreach ($itypes as $Id => $Val) : ?>
					<option value="<?php echo $Id; ?>"><?php echo $Val; ?></option>
<?php endforeach; ?>
				</select></dd>
		</dl>

		<input type="button" id="new-individual_reward" value="Nuevo" />

		<span><?php echo Text::get('tooltip project nindividual_reward'); ?></span><br />
	</fieldset>

	<h3>Recompensas individuales</h3>
<?php foreach ($project->individual_rewards as $individual) : ?>
				<fieldset><legend>recompensa <?php echo $individual->id; ?></legend>
        			<label>REMOVE! <input type="checkbox" name="remove-individual_reward<?php echo $individual->id; ?>" value="1" /></label><br />
					<span>Tipo: <?php echo $itypes[$individual->icon]; ?></span>

					<label>Cantidad solicitada (&euro;):<input type="text" id="individual_reward-amount<?php echo $individual->id; ?>" name="individual_reward-amount<?php echo $individual->id; ?>" value="<?php echo $individual->amount; ?>" /></label>
					<label>Unidades disponibles:<input type="text" id="individual_reward-units<?php echo $individual->id; ?>" name="individual_reward-units<?php echo $individual->id; ?>" value="<?php echo $individual->units; ?>" /></label>

					<dl>
						<dt><label for="individual_reward<?php echo $individual->id; ?>">Recompensa</label></dt>
						<dd><input type="text" id="individual_reward<?php echo $individual->id; ?>" name="individual_reward<?php echo $individual->id; ?>" value="<?php echo $individual->reward; ?>" /></dd>

						<dt><label for="individual_reward-description<?php echo $individual->id; ?>">Descripción</label></dt>
						<dd><textarea id="individual_reward-description<?php echo $individual->id; ?>" name="individual_reward-description<?php echo $individual->id; ?>" cols="50" rows="5"><?php echo $individual->description; ?></textarea></dd>
					</dl>

					<span><?php echo Text::get('tooltip project individual_reward'); ?></span><br />
				</fieldset>
<?php endforeach; ?>

				<input type="submit" name="submit" value="CONTINUAR" />
			</form>
<?php include 'view/project/footer.html.php' ?>