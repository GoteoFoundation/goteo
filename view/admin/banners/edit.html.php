<?php

use Goteo\Library\Text,
    Goteo\Model;

$banner = $this['banner'];

$node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

if ($node == \GOTEO_NODE) {
    // proyectos disponibles
    // si tenemos ya proyecto seleccionado lo incluimos
    $projects = Model\Banner::available($banner->project);
    $status = Model\Project::status();

    $image_size_txt = '700 x 156 (estricto)';
} else {
    $image_size_txt = '940 x 270 (estricto)';
}
?>
<form method="post" action="/admin/banners" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $this['action'] ?>" />
    <input type="hidden" name="order" value="<?php echo $banner->order ?>" />
    <input type="hidden" name="id" value="<?php echo $banner->id; ?>" />

<?php if ($node == \GOTEO_NODE) : ?>
<p>
    <label for="banner-project">Proyecto:</label><br />
    <select id="banner-project" name="project">
        <option value="" >Seleccionar el proyecto a mostrar en el banner</option>
    <?php foreach ($projects as $project) : ?>
        <option value="<?php echo $project->id; ?>"<?php if ($banner->project == $project->id) echo' selected="selected"';?>><?php echo $project->name . ' ('. $status[$project->status] . ')'; ?></option>
    <?php endforeach; ?>
    </select>
</p>
<?php else : ?>
<p>
    <label for="banner-name">Título:</label><br />
    <input type="text" name="title" id="banner-title" value="<?php echo $banner->title; ?>" size="50" />
</p>

<p>
    <label for="banner-description">Descripción:</label><br />
    <input type="text" name="description" id="banner-description" value="<?php echo $banner->description; ?>" size="85" />
</p>

<p>
    <label for="banner-url">Enlace:</label><br />
    <input type="text" name="url" id="banner-url" value="<?php echo $banner->url; ?>" size="85" />
</p>
<?php endif; ?>

<p>
    <label for="banner-image">Imagen de fondo: <?php echo $image_size_text; ?></label><br />
    <input type="file" id="banner-image" name="image" />
    <?php if (!empty($banner->image)) : ?>
        <br />
        <input type="hidden" name="prev_image" value="<?php echo $banner->image->id ?>" />
        <img src="<?php echo $banner->image->getLink() ?>" title="Fondo banner" alt="falta imagen"/>
    <?php endif; ?>
</p>

<p>
    <label>Publicado:</label><br />
    <label><input type="radio" name="active" id="banner-active" value="1"<?php if ($banner->active) echo ' checked="checked"'; ?>/> SÍ</label>
    &nbsp;&nbsp;&nbsp;
    <label><input type="radio" name="active" id="banner-inactive" value="0"<?php if (!$banner->active) echo ' checked="checked"'; ?>/> NO</label>
</p>

    <input type="submit" name="save" value="Guardar" />
</form>
