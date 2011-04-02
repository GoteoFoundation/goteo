<?php include 'view/project/header.html.php' ?>
<?php

 use Goteo\Library\Text; ?>
PROYECTO / Descripción<br />
GUÍA: <?php echo $guideText; ?><br />
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

        <dt>Categorías</dt>
        <dd><?php foreach ($categories as $Id => $Val) : ?>
            <label><input type="checkbox" name="categories[]" value="<?php echo $Id; ?>" <?php if (in_array($Id, $project->categories))
                echo ' checked="checked"'; ?> /><?php echo $Val; ?></label>
            <?php endforeach; ?>
        </dd>
        <span><?php echo Text::get('tooltip project category'); ?></span><br />

        <dt><label for="keywords">Palabras clave</label></dt>
        <dd>Añadir:<input type="text" id="keywords" name="keywords" value="<?php echo $project->keywords; ?>"/></dd>
        <span><?php echo Text::get('tooltip project keywords'); ?> (separadas por comas)</span><br />

        <dt><label for="media">Media</label></dt>
        <dd><input type="text" id="media" name="media" value="<?php echo $project->media; ?>"/></dd>
        <span><?php echo Text::get('tooltip project media'); ?> (url del video en vimeo o youtube)</span><br />

        <dt><label for="currently">Estado del proyecto</label></dt>
        <dd><select id="currently" name="currently">
        <?php foreach ($currently as $Id => $Val) : ?>
                <option value="<?php echo $Id; ?>" <?php if ($project->currently == $Id) echo ' selected="selected"'; ?>><?php echo $Val; ?></option>
        <?php endforeach; ?>
            </select>
        </dd>
        <span><?php echo Text::get('tooltip project currently'); ?> (estado de desarrollo actual del proyecto)</span><br />

        <dt><label for="project_location">Localización</label></dt>
        <dd><input type="text" id="project_location" name="project_location" value="<?php echo $project->project_location; ?>"/></dd>
        <span><?php echo Text::get('tooltip project project_location'); ?> (Si es solo online poner "online")</span><br />
    </dl>

    <input type="submit" name="submit" value="CONTINUAR" />
</form>
<?php include 'view/project/footer.html.php' ?>