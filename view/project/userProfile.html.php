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

        <div id="main" class="userProfile">

            <form method="post" action="">

                <?php echo new View('view/project/status.html.php', array('status' => $this['project']->status, 'progress' => $this['project']->progress)) ?>
                <?php echo new View('view/project/steps.html.php', array('steps' => $this['steps'], 'step' => $this['step'])) ?>

                <div class="superform">

                    <h3>Usuario/Perfil</h3>

                    <?php echo new View('view/project/guide.html.php', array('text' => $this['steps'][$this['step']]['guide'])) ?>
                    
                    <div class="fields">
                    
                    <ol class="fields">

                        <li class="field" id="field-name">
                            <div class="field">
                                <h4>Nombre del proyecto</h4>
                                <div class="tooltip" id="tooltip-name">
                                    <blockquote><?php echo Text::get('tooltip project name'); ?></blockquote>
                                </div>
                                <div class="elements"><input class="main" type="text" name="name" value="<?php if (isset($project->name)) echo htmlspecialchars($project->name) ?>" /></div>

                            </div>
                        </li>
                        
                    </ol>

                    <div class="buttons">
                        <input type="submit" value="Continuar" name="view-step-userPersonal" class="next" />
                    </div>

                </div>

                <?php include 'view/project/steps.html.php' ?>
                <?php include 'view/project/tooltips.js.php' ?>

            </form>

        </div>

    <?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>
<?php die; ?>
<!--
USUARIO / Perfil<br />
GUÍA: <?php // echo $guideText;  ?><br />
<hr />
<form action="/project/user" method="post">
	<dl>
		<dt><label for="name">Nombre completo</label></dt>
		<dd><input type="text" id="name" name="name" value="<?php echo $user->name; ?>"/></dd>
		<span><?php echo Text::get('tooltip user name'); ?></span><br />

		<dt><label for="image">Tu imagen</label></dt>
		<dd><input type="file" id="theimage" name="theimage" value=""/> img src="<?php echo $user->avatar; ?>" </dd>
		<input type="text" name="avatar" value="avatar.jpg" />
		<span><?php echo Text::get('tooltip user image'); ?></span><br />

		<dt><label for="about">Cu&eacute;ntanos algo sobre t&iacute;</label></dt>
		<dd><textarea id="about" name="about" cols="100" rows="10"><?php echo $user->about; ?></textarea></dd>
		<span><?php echo Text::get('tooltip user about'); ?></span><br />

		<dt>Intereses</dt>
		<dd><?php foreach ($interests as $Id=>$Val) : ?>
			<label><input type="checkbox" id="interests" name="interests[]" value="<?php echo $Id; ?>" <?php if (in_array($Id, $user->interests)) echo ' checked="checked"'; ?> /><?php echo $Val; ?></label>
		<?php endforeach; ?></dd>
		<span><?php echo Text::get('tooltip user interests'); ?></span><br />

		<dt><label for="keywords">Palabras clave</label></dt>
		<dd>Añadir:<input type="text" id="keywords" name="keywords" value="<?php echo $user->keywords; ?>"/>(separadas por comas)</dd>
		<span><?php echo Text::get('tooltip user keywords'); ?></span><br />

		<dt><label for="contribution">Qué podrías aportar a Goteo</label></dt>
		<dd><textarea id="contribution" name="contribution" cols="100" rows="10"><?php echo $user->contribution; ?></textarea></dd>
		<span><?php echo Text::get('tooltip user contribution'); ?></span><br />

		<dt><label for="blog">Blog</label></dt>
		<dd>http://<input type="text" id="blog" name="blog" value="<?php echo $user->blog; ?>"/></dd>
		<span><?php echo Text::get('tooltip user blog'); ?></span><br />

		<dt><label for="twitter">Twitter</label></dt>
		<dd>http://twitter.com/<input type="text" id="twitter" name="twitter" value="<?php echo $user->twitter; ?>"/></dd>
		<span><?php echo Text::get('tooltip user twitter'); ?></span><br />

		<dt><label for="facebook">Facebook</label></dt>
		<dd>http://facebook.com/<input type="text" id="facebook" name="facebook" value="<?php echo $user->facebook; ?>"/></dd>
		<span><?php echo Text::get('tooltip user facebook'); ?></span><br />

		<dt><label for="linkedin">Linkedin</label></dt>
		<dd>http://linkedin.com/<input type="text" id="linkedin" name="linkedin" value="<?php echo $user->linkedin; ?>"/></dd>
		<span><?php echo Text::get('tooltip user linkedin'); ?></span><br />

	</dl>
