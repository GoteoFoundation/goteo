<?php include 'view/header.html.php' ?>
<?php include 'view/project/header.html.php' ?>
<?php use Goteo\Library\Text; ?>
PROYECTO / Descripción<br />
GUÍA: <?php echo $guideText;  ?><br />
<?php include 'view/project/errors.html.php' ?>
<hr />
<form action="/project/edit" method="post">
	<dl>
		<dt><label for="name">Nombre del proyecto</label></dt>
		<dd><input type="text" id="name" name="name" value="<?php echo $project->name; ?>"/></dd>
		<span><?php echo Text::get('tooltip project name'); ?></span><br />

		<dt><label for="image">Imagen del proyecto</label></dt>
		<dd><input type="file" id="theimage" name="theimage" value=""/> img src="<?php echo $project->image; ?>" </dd>
		<input type="text" name="image" value="project.jpg" />
		<span><?php echo Text::get('tooltip project image'); ?></span><br />

		<dt><label for="description">Descripción</label></dt>
		<dd><textarea id="description" name="description" cols="50" rows="5"><?php echo $project->description; ?></textarea></dd>
		<span><?php echo Text::get('tooltip project description'); ?></span><br />

		<dt><label for="motivation">Motivación</label></dt>
		<dd><textarea id="motivation" name="motivation" cols="50" rows="5"><?php echo $project->motivation; ?></textarea></dd>
		<span><?php echo Text::get('tooltip project motivation'); ?></span><br />

		<dt><label for="about">Qué és</label></dt>
		<dd><textarea id="about" name="about" cols="50" rows="5"><?php echo $project->about; ?></textarea></dd>
		<span><?php echo Text::get('tooltip project about'); ?></span><br />

		<dt><label for="goal">Objetivos</label></dt>
		<dd><textarea id="goal" name="goal" cols="50" rows="5"><?php echo $project->goal; ?></textarea></dd>
		<span><?php echo Text::get('tooltip project goal'); ?></span><br />

		<dt><label for="related">Experiencia relacionada y equipo</label></dt>
		<dd><textarea id="related" name="related" cols="50" rows="5"><?php echo $project->related; ?></textarea></dd>
		<span><?php echo Text::get('tooltip project related'); ?></span><br />

		<dt><label for="category">Categoría</label></dt>
		<dd><input type="text" id="category" name="category" value="<?php echo $project->category; ?>"/></dd>
		<span><?php echo Text::get('tooltip project category'); ?></span><br />

		<dt><label for="media">Media</label></dt>
		<dd><input type="text" id="media" name="media" value="<?php echo $project->media; ?>"/></dd>
		<span><?php echo Text::get('tooltip project media'); ?></span><br />

		<dt><label for="keywords">Palabras clave</label></dt>
		<dd>Añadir:<input type="text" id="keywords" name="keywords" value=""/>(separadas por comas)</dd>
		<input type="button" id="new-keyword" value="Nuevo" />
		<div><?php foreach ($project->keywords as $keyword) : ?>
			<span><a href="#remove-keyword-<?php echo $keyword->id; ?>">(X)</a> <?php echo $keyword->keyword; ?></span>
		<?php endforeach; ?></div>
		<span><?php echo Text::get('tooltip project keywords'); ?></span><br />

		<dt><label for="currently">Estado del proyecto</label></dt>
		<dd><select id="currently" name="currently">
				<?php foreach ($currents as $curId=>$curVal) : ?>
				<option value="<?php echo $curId; ?>" <?php if ($project->currently == $curId) echo ' selected="selected"'; ?>><?php echo $curVal; ?></option>
				<?php endforeach; ?>
			</select></dd>
		<span><?php echo Text::get('tooltip project currently'); ?></span><br />

		<dt><label for="project_location">Localización</label></dt>
		<dd><input type="text" id="project_location" name="project_location" value="<?php echo $project->project_location; ?>"/></dd>
		<span><?php echo Text::get('tooltip project project_location'); ?></span><br />

	</dl>
	<input type="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>
<?php include 'view/footer.html.php' ?>