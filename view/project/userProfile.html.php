<?php include 'view/project/header.html.php' ?>
<?php use Goteo\Library\Text; ?>
USUARIO / Perfil<br />
GUÍA: <?php echo $guideText;  ?><br />
<hr />
<form action="/project/user" method="post">
	<dl>
		<dt><label for="name">Nombre completo</label></dt>
		<dd><input type="text" id="name" name="name" value="<?php echo $user->name; ?>"/></dd>
        <?php if ($project->itsok('user-name')) echo 'OK'; else echo $project->errors['user-name']; ?>
		<span><?php echo Text::get('tooltip user name'); ?></span><br />

		<dt><label for="image">Tu imagen</label></dt>
		<dd><input type="file" id="theimage" name="theimage" value=""/> img src="<?php echo $user->avatar; ?>" </dd>
		<input type="text" name="avatar" value="avatar.jpg" />
        <?php if ($project->itsok('user-avatar')) echo 'OK'; else echo $project->errors['user-avatar']; ?>
		<span><?php echo Text::get('tooltip user image'); ?></span><br />

		<dt><label for="about">Cu&eacute;ntanos algo sobre t&iacute;</label></dt>
		<dd><textarea id="about" name="about" cols="100" rows="10"><?php echo $user->about; ?></textarea></dd>
        <?php if ($project->itsok('user-about')) echo 'OK'; else echo $project->errors['user-about']; ?>
		<span><?php echo Text::get('tooltip user about'); ?></span><br />

		<dt>Intereses</dt>
		<dd><?php foreach ($interests as $Id=>$Val) : ?>
			<label><input type="checkbox" id="interests" name="interests[]" value="<?php echo $Id; ?>" <?php if (in_array($Id, $user->interests)) echo ' checked="checked"'; ?> /><?php echo $Val; ?></label>
		<?php endforeach; ?></dd>
        <?php if ($project->itsok('user-interests')) echo 'OK'; else echo $project->errors['user-interests']; ?>
		<span><?php echo Text::get('tooltip user interests'); ?></span><br />

		<dt><label for="keywords">Palabras clave</label></dt>
		<dd>Añadir:<input type="text" id="keywords" name="keywords" value="<?php echo $user->keywords; ?>"/>(separadas por comas)</dd>
        <?php if ($project->itsok('user-keywords')) echo 'OK'; else echo $project->errors['user-keywords']; ?>
		<span><?php echo Text::get('tooltip user keywords'); ?></span><br />

		<dt><label for="contribution">Qué podrías aportar a Goteo</label></dt>
		<dd><textarea id="contribution" name="contribution" cols="100" rows="10"><?php echo $user->contribution; ?></textarea></dd>
        <?php if ($project->itsok('user-contribution')) echo 'OK'; else echo $project->errors['user-contribution']; ?>
		<span><?php echo Text::get('tooltip user contribution'); ?></span><br />

		<dt><label for="blog">Blog</label></dt>
		<dd>http://<input type="text" id="blog" name="blog" value="<?php echo $user->blog; ?>"/></dd>
        <?php if ($project->itsok('user-blog')) echo 'OK'; else echo $project->errors['user-blog']; ?>
		<span><?php echo Text::get('tooltip user blog'); ?></span><br />

		<dt><label for="twitter">Twitter</label></dt>
		<dd>http://twitter.com/<input type="text" id="twitter" name="twitter" value="<?php echo $user->twitter; ?>"/></dd>
        <?php if ($project->itsok('user-twitter')) echo 'OK'; else echo $project->errors['user-twitter']; ?>
		<span><?php echo Text::get('tooltip user twitter'); ?></span><br />

		<dt><label for="facebook">Facebook</label></dt>
		<dd>http://facebook.com/<input type="text" id="facebook" name="facebook" value="<?php echo $user->facebook; ?>"/></dd>
        <?php if ($project->itsok('user-facebook')) echo 'OK'; else echo $project->errors['user-facebook']; ?>
		<span><?php echo Text::get('tooltip user facebook'); ?></span><br />

		<dt><label for="linkedin">Linkedin</label></dt>
		<dd>http://linkedin.com/<input type="text" id="linkedin" name="linkedin" value="<?php echo $user->linkedin; ?>"/></dd>
        <?php if ($project->itsok('user-linkedin')) echo 'OK'; else echo $project->errors['user-linkedin']; ?>
		<span><?php echo Text::get('tooltip user linkedin'); ?></span><br />

	</dl>
	<input type="submit" name="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>