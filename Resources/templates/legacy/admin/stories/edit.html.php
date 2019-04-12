<?php

use Goteo\Library\Text,
    Goteo\Model;

$story = $vars['story'];

$image=$story->getImage();

$pool_image=$story->getPoolImage();

$text_positions = $vars['text_positions'];

$spheres = $vars['spheres'];


$items = array();
$node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;
// $iId = id del post
// $iObj = titulo
foreach ($vars['items'] as $iId=>$iObj) {
    $el_val = str_replace(array("'", '"'), '`', $iObj)." ({$iId})";
    $items[] = '{ value: "'.$el_val.'", id: "'.$iId.'" }';
    if ($iId == $story->post) $preVal = "$el_val";
}

// proyectos disponibles
// si tenemos ya proyecto seleccionado lo incluimos
$projects = Model\Stories::available();

$status = Model\Project::status();
$langs = \Goteo\Application\Lang::listAll('name', false);
?>
<div class="widget board">
    <form method="post" action="/admin/stories/<?php echo $vars['action'] ?>" enctype="multipart/form-data">
        <input type="hidden" name="action" value="<?php echo $vars['action'] ?>" />
        <input type="hidden" name="order" value="<?php echo $story->order ?>" />
        <input type="hidden" name="id" value="<?php echo $story->id; ?>" />

        <input type="hidden" id="item" name="item" value="<?php echo $story->post; ?>" />

        <p>
            <label for="story-project">Proyecto: (No seleccionar si es banner general)</label><br />
            <select id="story-project" name="project" style="width:67%">
                <option value="" >Seleccionar el proyecto a mostrar en la historia exitosa</option>
            <?php foreach ($projects as $project) : ?>
                <option value="<?php echo $project->id; ?>"<?php if ($story->project_id == $project->id) echo' selected="selected"';?>><?php echo htmlspecialchars($project->name) . ' ('. $status[$project->status] . ')'; ?></option>
            <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="story-lang">Idioma:</label><br />
            <select id="story-lang" name="lang">
            <?php foreach ($langs as $id => $name) : ?>
                <option value="<?php echo $id; ?>"<?php if ($story->lang == $id) echo' selected="selected"';?>><?php echo $name; ?></option>
            <?php endforeach; ?>
            </select>
        </p>

    <div id="text-story">
    <p>
        <label for="story-name">Autor/T&iacute;tulo:</label><br />
        <input type="text" name="title" id="story-title" value="<?php echo $story->title; ?>" size="60" />
    </p>

    <p>
        <label for="story-review">Review de Goteo: (Subtítulo)</label><br />
        <textarea id="story-review" name="review" cols="60" rows="2"><?php echo $story->review; ?></textarea>
    </p>

     <p>
            <label for="story-project">Posición del texto: (Solo si es banner general)</label><br />
            <select id="story-project" name="text_position" style="width:30%">
            <?php foreach ($text_positions as $positionId => $position) :  ?>
                <option value="<?= $positionId ?>"<?php if ($story->text_position == $positionId) echo' selected="selected"';?>><?= $position  ?></option>
            <?php endforeach; ?>
            </select>
        </p>

    <p>
        <label for="story-description">Testimonio:</label><br />
        <textarea id="story-description" name="description" cols="60" rows="2"><?php echo $story->description; ?></textarea>
    </p>

    <p>
        <label for="story-post">Post: (autocomplete título o numero)</label><br />
        <input type="text" name="post" id="story-post" value="<?php echo $preVal; ?>" size="60" />
    </p>

    <p>
        <label for="story-url">Enlace:</label><br />
        <input type="text" name="url" id="story-url" value="<?php echo $story->url; ?>" size="60" />
    </p>
    </div>

    <p>
        <label for="story-image">Imagen de fondo: 600x400</label><br />
        <input type="file" id="story-image" name="image" />
        <?php if ($image->name) : ?>
            <br />
            <input type="hidden" name="prev_image" value="<?php echo $image->name ?>" />
            <img src="<?php echo $image->getLink(940, 385, true) ?>" title="Fondo historia" alt="falta imagen"/>
            <input type="submit" name="image-remove" value="Quitar" />
        <?php endif; ?>
    </p>
    <h2>Para landings</h2>
     <p>
        <label for="story-pool-image">Imagen landing (polaroid): </label><br />
        <input type="file" id="story-pool-image" name="pool_image" />
        <?php if ($pool_image->name) : ?>
            <br />
            <input type="hidden" name="prev_pool_image" value="<?php echo $pool_image->name ?>" />
            <img src="<?php echo $pool_image->getLink(940, 385, true) ?>" title="Imagen landing monedero" alt="falta imagen"/>
            <input type="submit" name="image-pool-remove" value="Quitar" />
        <?php endif; ?>
    </p>
    <p>
        <label>Landing monedero (Mostrar)</label><br />
        <label><input type="radio" name="pool" id="pool-active" value="1"<?php if ($story->pool) echo ' checked="checked"'; ?>/> S&Iacute;</label>
        &nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="pool" id="pool-inactive" value="0"<?php if (!$story->pool) echo ' checked="checked"'; ?>/> NO</label>
    </p>
    <p>
        <label>Landing pitch (Mostrar)</label><br />
        <label><input type="radio" name="landing_pitch" id="landing-pitch-active" value="1"<?php if ($story->landing_pitch) echo ' checked="checked"'; ?>/> S&Iacute;</label>
        &nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="landing_pitch" id="landing-pitch-inactive" value="0"<?php if (!$story->landing_pitch) echo ' checked="checked"'; ?>/> NO</label>
    </p>
    <p>
        <label>Landing match (Mostrar)</label><br />
        <label><input type="radio" name="landing_match" id="landing-match-active" value="1"<?php if ($story->landing_match) echo ' checked="checked"'; ?>/> S&Iacute;</label>
        &nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="landing_match" id="landing-match-inactive" value="0"<?php if (!$story->landing_match) echo ' checked="checked"'; ?>/> NO</label>
    </p>
    <p>
        <label for="story-type">Tipo de protagonista: (Para mostrar en landing)</label><br />
        <select id="story-type" name="type" style="width:30%">
                <option value="" >Seleccionar un tipo</option>
            <?php foreach ($story::getListTypes() as $id => $type) :  ?>
                <option value="<?= $id ?>"<?php if ($story->type == $id) echo' selected="selected"';?>><?= Text::get($type)  ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="story-sphere">Ámbito</label><br />
        <select required="required" id="story-sphere" name="sphere" style="width:30%">
            <?php foreach ($spheres as $sphere) :  ?>
                <option value="<?= $sphere->id ?>"<?php if ($story->sphere == $sphere->id) echo' selected="selected"';?>><?= Text::get($sphere->name)  ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>


    <p>
        <label>Publicado en home:</label><br />
        <label><input type="radio" name="active" id="story-active" value="1"<?php if ($story->active) echo ' checked="checked"'; ?>/> S&Iacute;</label>
        &nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="active" id="story-inactive" value="0"<?php if (!$story->active) echo ' checked="checked"'; ?>/> NO</label>
    </p>

        <input type="submit" name="save" value="Guardar" />

        <p>
            <label for="mark-pending">Marcar como pendiente de traducir</label>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </p>

    </form>
</div>
<!-- End widget-board -->

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function () {

    var items = [<?php echo implode(', ', $items); ?>];

    /* Autocomplete para elementos */
    $( "#story-post" ).autocomplete({
      source: items,
      minLength: 1,
      autoFocus: true,
      select: function( event, ui) {
                $("#item").val(ui.item.id);
            }
    });

});
// @license-end
</script>
