<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'project-form';

$project = $this['project'];

$stepsView = new View('view/project/steps.html.php', array('steps' => $this['steps'], 'step' => $this['step']));

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Formulario</h2>
            </div>
        </div>

        <div id="main" class="userProfile">

            <form method="post" action="">

                <?php echo new View('view/project/status.html.php', array('status' => $project->status, 'progress' => $project->progress)) ?>
                <?php echo $stepsView ?>

                <div class="superform">

                    <h3>Usuario/Perfil</h3>

                    <?php echo new View('view/project/guide.html.php', array('text' => $this['steps'][$this['step']]['guide'])) ?>
                    
                    <div class="fields">
                    
                       <?php //@INTRUSION JULIAN!!! para usarlo sin maquetaciÃ³n
                    if ($this['nodesign'] == true) :
                        $project = $this['project'];
                        $user = $this['user'];
                        $interests = $this['interests'];
                        ?>
            <?php if (!empty($project->errors['userProfile'])) :
                echo '<p>';
                foreach ($project->errors['userProfile'] as $campo=>$error) : ?>
                    <span style="color:red;"><?php echo "$campo: $error"; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>
	<dl>
		<dt><label for="name">Nombre completo</label></dt>
		<dd><input type="text" id="name" name="user_name" value="<?php echo $user->name; ?>"/></dd>
		<span><?php echo Text::get('tooltip user name'); ?></span><br />

		<dt><label for="image">Tu imagen</label></dt>
		<dd><input type="file" id="theimage" name="theimage" value=""/> img src="<?php echo $user->avatar; ?>" </dd>
		<input type="text" name="user_avatar" value="avatar.jpg" />
		<span><?php echo Text::get('tooltip user image'); ?></span><br />

		<dt><label for="about">Cu&eacute;ntanos algo sobre t&iacute;</label></dt>
		<dd><textarea id="about" name="user_about" cols="100" rows="10"><?php echo $user->about; ?></textarea></dd>
		<span><?php echo Text::get('tooltip user about'); ?></span><br />

		<dt>Intereses</dt>
		<dd><?php foreach ($interests as $Id=>$Val) : ?>
			<label><input type="checkbox" id="interests" name="interests[]" value="<?php echo $Id; ?>" <?php if (in_array($Id, $user->interests)) echo ' checked="checked"'; ?> /><?php echo $Val; ?></label>
		<?php endforeach; ?></dd>
		<span><?php echo Text::get('tooltip user interests'); ?></span><br />

		<dt><label for="keywords">Palabras clave</label></dt>
		<dd>AÃ±adir:<input type="text" id="keywords" name="user_keywords" value="<?php echo $user->keywords; ?>"/>(separadas por comas)</dd>
		<span><?php echo Text::get('tooltip user keywords'); ?></span><br />

		<dt><label for="contribution">QuÃ© podrÃ­as aportar a Goteo</label></dt>
		<dd><textarea id="contribution" name="user_contribution" cols="100" rows="10"><?php echo $user->contribution; ?></textarea></dd>
		<span><?php echo Text::get('tooltip user contribution'); ?></span><br />

		<dt><label for="nweb">Nueva web</label></dt>
		<dd>http://<input type="text" id="nweb" name="nweb" value=""/></dd>
        <?php foreach ($user->webs as $web) : ?>
            <label>REMOVE! <input type="checkbox" name="remove-web<?php echo $web->id; ?>" value="1" /></label>
            <label><input type="text" name="web<?php echo $web->id; ?>" value="<?php echo $web->url; ?>" /></label>
            <hr />
        <?php endforeach; ?>
		<span><?php echo Text::get('tooltip user blog'); ?></span><br />

		<dt><label for="twitter">Twitter</label></dt>
		<dd>http://twitter.com/<input type="text" id="twitter" name="user_twitter" value="<?php echo $user->twitter; ?>"/></dd>
		<span><?php echo Text::get('tooltip user twitter'); ?></span><br />

		<dt><label for="facebook">Facebook</label></dt>
		<dd>http://facebook.com/<input type="text" id="facebook" name="user_facebook" value="<?php echo $user->facebook; ?>"/></dd>
		<span><?php echo Text::get('tooltip user facebook'); ?></span><br />

		<dt><label for="linkedin">Linkedin</label></dt>
		<dd>http://linkedin.com/<input type="text" id="linkedin" name="user_linkedin" value="<?php echo $user->linkedin; ?>"/></dd>
		<span><?php echo Text::get('tooltip user linkedin'); ?></span><br />

	</dl>
                    <?php endif ?>
                        
                    </div>

                    <div class="buttons">
                        <input type="submit" value="Continuar" name="view-step-userPersonal" class="next" />
                    </div>

                </div>

                <?php echo $stepsView ?>
                <?php include 'view/project/tooltips.js.php' ?>

            </form>

        </div>

    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>