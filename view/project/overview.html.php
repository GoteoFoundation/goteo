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

        <form method="post" action="">

            <?php include 'view/project/status.html.php' ?>
            <?php include 'view/project/steps.html.php' ?>

            <div class="form">

                <h3>Proyecto/Descripción</h3>

                <?php include 'view/project/guide.html.php' ?>

                <ol class="fields">

                    <li class="name"><h4>Nombre del proyecto</h4>
                        <input type="text" name="name" />
                    </li>

                    <li class="images"><h4>Imágenes del proyecto</h4></li>

                    <li class="summary">
                        <h4>Descripción</h4>
                        <textarea></textarea>
                    </li>
                    
                    <li class="category">
                        <h4>Categorías</h4>                        
                    </li>
                    
                    <li class="keywords">
                        <h4>Palabras clave</h4>                        
                        <input type="text" name="keywords" />
                    </li>
                    
                    <li class="video">
                        <h4>Vídeo</h4>                        
                        <input type="text" name="video" />
                    </li>
                    
                    <li class="projectstatus">
                        <h4>Estado actual del proyecto</h4>
                        
                        <label><input type="radio" name="projectstatus" value="0" />
                        Inicial</label>
                        
                        <label><input type="radio" name="projectstatus" value="1" />
                        Medio</label>
                        
                        <label><input type="radio" name="projectstatus" value="2" />
                        Avanzado</label>
                        
                        <label><input type="radio" name="projectstatus" value="3" />
                        Finalizado</label>
                        
                    </li>
                    
                    <li class="location">
                        <h4>Localización</h4>
                        <input type="text" name="location" />
                        <label><input type="checkbox" name="onlineonly" value="1" />
                        Solo Internet</label>
                    </li>

                </ol>

                <div class="buttons">
                    <input type="submit" value="Continuar" />
                </div>

            </div>

            <?php include 'view/project/steps.html.php' ?>

        </form>

    </div>

    <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>

<!--

PROYECTO / Descripción<br />
GUÍA: <?php echo $guideText; ?><br />
<hr />
<form action="/project/edit" method="post">
    <dl>
        <dt><label for="name">Nombre del proyecto</label></dt>
        <dd><input type="text" id="name" name="name" value="<?php echo $project->name; ?>"/></dd>
        <?php if ($project->itsok('name')) echo 'OK'; else echo $project->errors['name']; ?>
        <span><?php echo Text::get('tooltip project name'); ?></span><br />

        <dt><label for="image">Imagen del proyecto</label></dt>
        <dd><input type="file" id="theimage" name="theimage" value=""/> img src="<?php echo $project->image; ?>" </dd>
        <input type="text" name="image" value="project.jpg" />
        <?php if ($project->itsok('image')) echo 'OK'; else echo $project->errors['image']; ?>
        <span><?php echo Text::get('tooltip project image'); ?></span><br />

        <dt><label for="description">Descripción</label></dt>
        <dd><textarea id="description" name="description" cols="50" rows="5"><?php echo $project->description; ?></textarea></dd>
        <?php if ($project->itsok('description')) echo 'OK'; else echo $project->errors['description']; ?>
        <span><?php echo Text::get('tooltip project description'); ?></span><br />

        <dt><label for="motivation">Motivación</label></dt>
        <dd><textarea id="motivation" name="motivation" cols="50" rows="5"><?php echo $project->motivation; ?></textarea></dd>
        <?php if ($project->itsok('motivation')) echo 'OK'; else echo $project->errors['motivation']; ?>
        <span><?php echo Text::get('tooltip project motivation'); ?></span><br />

        <dt><label for="about">Qué és</label></dt>
        <dd><textarea id="about" name="about" cols="50" rows="5"><?php echo $project->about; ?></textarea></dd>
        <?php if ($project->itsok('about')) echo 'OK'; else echo $project->errors['about']; ?>
        <span><?php echo Text::get('tooltip project about'); ?></span><br />

        <dt><label for="goal">Objetivos</label></dt>
        <dd><textarea id="goal" name="goal" cols="50" rows="5"><?php echo $project->goal; ?></textarea></dd>
        <?php if ($project->itsok('goal')) echo 'OK'; else echo $project->errors['goal']; ?>
        <span><?php echo Text::get('tooltip project goal'); ?></span><br />

        <dt><label for="related">Experiencia relacionada y equipo</label></dt>
        <dd><textarea id="related" name="related" cols="50" rows="5"><?php echo $project->related; ?></textarea></dd>
        <?php if ($project->itsok('related')) echo 'OK'; else echo $project->errors['related']; ?>
        <span><?php echo Text::get('tooltip project related'); ?></span><br />

        <dt>Categorías</dt>
        <dd><?php foreach ($categories as $Id => $Val) : ?>
            <label><input type="checkbox" id="categories" name="categories[]" value="<?php echo $Id; ?>" <?php if (in_array($Id, $project->categories))
                echo ' checked="checked"'; ?> /><?php echo $Val; ?></label>
            <?php endforeach; ?>
        </dd>
        <?php if ($project->itsok('categories')) echo 'OK'; else echo $project->errors['categories']; ?>
        <span><?php echo Text::get('tooltip project category'); ?></span><br />

        <dt><label for="keywords">Palabras clave</label></dt>
        <dd>Añadir:<input type="text" id="keywords" name="keywords" value="<?php echo $project->keywords; ?>"/></dd>
        <?php if ($project->itsok('keywords')) echo 'OK'; else echo $project->errors['keywords']; ?>
        <span><?php echo Text::get('tooltip project keywords'); ?> (separadas por comas)</span><br />

        <dt><label for="media">Media</label></dt>
        <dd><input type="text" id="media" name="media" value="<?php echo $project->media; ?>"/></dd>
        <?php if ($project->itsok('media')) echo 'OK'; else echo $project->errors['media']; ?>
        <span><?php echo Text::get('tooltip project media'); ?> (url del video en vimeo o youtube)</span><br />

        <dt><label for="currently">Estado del proyecto</label></dt>
        <dd><select id="currently" name="currently">
        <?php foreach ($currently as $Id => $Val) : ?>
                <option value="<?php echo $Id; ?>" <?php if ($project->currently == $Id) echo ' selected="selected"'; ?>><?php echo $Val; ?></option>
        <?php endforeach; ?>
            </select>
        </dd>
        <?php if ($project->itsok('currently')) echo 'OK'; else echo $project->errors['currently']; ?>
        <span><?php echo Text::get('tooltip project currently'); ?> (estado de desarrollo actual del proyecto)</span><br />

        <dt><label for="project_location">Localización</label></dt>
        <dd><input type="text" id="project_location" name="project_location" value="<?php echo $project->project_location; ?>"/></dd>
        <?php if ($project->itsok('project_location')) echo 'OK'; else echo $project->errors['project_location']; ?>
        <span><?php echo Text::get('tooltip project project_location'); ?> (Si es solo online poner "online")</span><br />
    </dl>

    <input type="submit" name="submit" value="CONTINUAR" />
</form>
-->