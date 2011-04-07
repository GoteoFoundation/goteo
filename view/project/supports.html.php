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

        <div id="main" class="supports">

            <form method="post" action="">

                <?php include 'view/project/status.html.php' ?>
                <?php include 'view/project/steps.html.php' ?>

                <div class="superform green">

                    <h3>Proyecto/Colaboraciones</h3>

                    <?php include 'view/project/guide.html.php' ?>
                                        

                    <div class="buttons">
                        <input type="hidden" name="step" value="rewards" />
                        <input type="submit" value="Continuar" />
                    </div>

                </div>

                <?php include 'view/project/steps.html.php' ?>

            </form>

        </div>
                
    <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>
<!--
PROYECTO / Colaboraciones<br />
GUÍA: <?php echo $guideText;  ?><br />
<?php include 'view/project/errors.html.php' ?>
<hr />
<form action="/project/supports" method="post">
		<fieldset>
			<legend>Nueva colaboración</legend>
			<dl>
				<dt><label for="nsupport">Colaboración</label></dt>
				<dd><input type="text" id="nsupport" name="nsupport" value=""/></dd>

				<dt><label for="nsupport-description">Descripción</label></dt>
				<dd><textarea id="nsupport-description" name="nsupport-description" cols="50" rows="5"></textarea></dd>
				
				<dt><label for="nsupport-type">Tipo</label></dt>
				<dd><select id="nsupport-type" name="nsupport-type">
					<?php foreach ($types as $Id => $Val) : ?>
						<option value="<?php echo $Id; ?>"><?php echo $Val; ?></option>
					<?php endforeach; ?>
					</select></dd>

			</dl>
			<span><?php echo Text::get('tooltip project nsupport'); ?></span><br />
		</fieldset>

		<?php foreach ($project->supports as $support) : ?>
			<fieldset>
				<legend>Colaboración <?php echo $support->id; ?></legend>
				<label>REMOVE! <input type="checkbox" name="remove-support<?php echo $support->id; ?>" value="1" /></label>
				<dl>
					<dt><label for="support<?php echo $support->id; ?>">Colaboración</label></dt>
					<dd><input type="text" id="support<?php echo $support->id; ?>" name="support<?php echo $support->id; ?>" value="<?php echo $support->support; ?>"/></dd>
                    <?php if ($project->itsok('support'.$support->id)) echo 'OK'; else echo $project->errors['support'.$support->id]; ?>

					<dt><label for="support-description<?php echo $support->id; ?>">Descripción</label></dt>
					<dd><textarea id="support-description<?php echo $support->id; ?>" name="support-description<?php echo $support->id; ?>" cols="50" rows="5"><?php echo $support->description; ?></textarea></dd>
                    <?php if ($project->itsok('support-description'.$support->id)) echo 'OK'; else echo $project->errors['support-description'.$support->id]; ?>

					<dt><label for="support-type<?php echo $support->id; ?>">Tipo</label></dt>
					<dd><select id="support-type<?php echo $support->id; ?>" name="support-type<?php echo $support->id; ?>">
						<?php foreach ($types as $Id => $Val) : ?>
							<option value="<?php echo $Id; ?>"<?php if ($support->type==$Id) echo ' selected="selected"'; ?>><?php echo $Val; ?></option>
						<?php endforeach; ?>
						</select></dd>
				</dl>
				<span><?php echo Text::get('tooltip project support'); ?></span><br />
			</fieldset>
		<?php endforeach; ?>

	<input type="submit" name="submit" value="CONTINUAR" />
</form>
-->