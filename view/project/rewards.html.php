<?php 

use Goteo\Library\Text;

$bodyClass = 'project-form';

include 'view/prologue.html.php';
    
    include 'view/header.html.php'; ?>

    <div id="sub-header">
        <div>
            <h2>Formulario</h2>
        </div>
    </div>

    <div id="main" class="overview">

        <form method="post" action="" class="project">

            <?php include 'view/project/status.html.php' ?>
            <?php include 'view/project/steps.html.php' ?>

            <div class="superform aqua">

                <h3>Proyecto/Retornos</h3>

                <?php include 'view/project/guide.html.php' ?>
                
                <div class="fields">

                    <ol class="fields">

                        <li class="field" id="field-social_reward">
                            <div class="field">
                                <h4>Retornos colectivos</h4>
                                <div class="tooltip" id="tooltip-social_reward">
                                    <blockquote><?php echo Text::get('tooltip project social_reward'); ?></blockquote>
                                </div>
                                <div class="elements">                                    
                                    <input class="add" type="submit" value="Añadir" />                                    
                                </div>                                
                            </div>
                        </li>

                        <li class="field" id="field-nsocial_reward"><h4>Recompensas individuales</h4>
                            <div class="field">
                                <h4>Retornos colectivos</h4>
                                <div class="tooltip" id="tooltip-nsocial_reward">
                                    <blockquote><?php echo Text::get('tooltip project nsocial_reward'); ?></blockquote>
                                </div>
                                <div class="elements">
                                    <input class="add" type="submit" value="Añadir" />                                    
                                </div>                                
                            </div>                            
                        </li>                                        

                    </ol>
                    
                </div>

                <div class="buttons">
                    <input class="next" type="submit" name="view-step-supports" value="Continuar"  />
                </div>

            </div>

            <?php include 'view/project/steps.html.php' ?>
            
            <?php include 'view/project/tooltips.js.php' ?>            

        </form>

    </div>

    <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>

<!--
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

			<dt><label for="nsocial_reward-license">Licencia</label></dt>
			<dd><select id="nsocial_reward-license" name="nsocial_reward-license">
                    <option value="">--</option>
                <?php foreach ($licenses as $Id => $Val) : ?>
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

                <dt><label for="social_reward-license<?php echo $social->id; ?>">Licencia</label></dt>
                <dd><select id="social_reward-license<?php echo $social->id; ?>" name="social_reward-license<?php echo $social->id; ?>">
                    <option value="">--</option>
                <?php foreach ($licenses as $Id => $Val) : ?>
                    <option value="<?php echo $Id; ?>"<?php if ($social->license == $Id) echo ' selected="selected"'; ?>><?php echo $Val; ?></option>
                <?php endforeach; ?>
                </select></dd>
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
-->