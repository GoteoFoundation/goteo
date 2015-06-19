<?php

use Goteo\Library\Text,
    Goteo\Model;

$banner = $vars['banner'];

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

//para autocomplete
$items = array();

foreach ($projects as $project) {
    $items[] = '{ value: "' . str_replace('"', '\"', $project->name) . '", id: "' . $project->id . '" }';
}
?>
<form method="post" action="/admin/banners/save/<?php echo $banner->id; ?>" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $vars['action'] ?>"/>
    <input type="hidden" name="order" value="<?php echo $banner->order ?>"/>
    <input type="hidden" name="id" value="<?php echo $banner->id; ?>"/>
    <input type="hidden" id="item" name="item" value="<?php echo $banner->project; ?>"/>

    <?php if ($node == \GOTEO_NODE) : ?>

        <p>
            <label for="banner-project">Proyecto: (autocomplete nombre)</label><br/>
            <input type="text" name="project" id="banner-project" value="<?php echo $banner->name; ?>" size="60"/>
        </p>

        <script type="text/javascript">
            /* para ocultar los campos de texto al seleccionar proyecto*/
            $(function () {

                $("#banner-project").change(function () {
                    if ($(this).val() != '') {
                        $("#text-banner").hide();
                    } else {
                        $("#text-banner").show();
                    }
                });

            });

        </script>
    <?php endif; ?>

    <div id="text-banner"<?php if (!empty($banner->project)) echo ' style="display: none;"'; ?>>
        <p>
            <label for="banner-name">T&iacute;tulo:</label><br/>
            <input type="text" name="title" id="banner-title" value="<?php echo $banner->title; ?>" size="50"/>
        </p>

        <p>
            <label for="banner-description">Descripci&oacute;n:</label><br/>
            <input type="text" name="description" id="banner-description" value="<?php echo $banner->description; ?>"
                   size="85"/>
        </p>

        <p>
            <label for="banner-url">Enlace:</label><br/>
            <input type="text" name="url" id="banner-url" value="<?php echo $banner->url; ?>" size="85"/>
        </p>
    </div>

    <p>
        <label for="banner-image">Imagen de fondo: <?php echo $image_size_txt; ?></label><br/>
        <input type="file" id="banner-image" name="image"/>
        <?php if (!empty($banner->image)) : ?>
            <br/>
            <input type="hidden" name="prev_image" value="<?php echo $banner->image->id ?>"/>
            <img src="<?php echo $banner->image->getLink(700, 150, true) ?>" title="Fondo banner" alt="falta imagen"/>
            <input type="submit" name="image-<?php echo $banner->image->hash; ?>-remove" value="Quitar" />
        <?php endif; ?>
    </p>

    <p>
        <label>Publicado:</label><br/>
        <label><input type="radio" name="active" id="banner-active"
                      value="1"<?php if ($banner->active) echo ' checked="checked"'; ?>/> S&Iacute;</label>
        &nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="active" id="banner-inactive"
                      value="0"<?php if (!$banner->active) echo ' checked="checked"'; ?>/> NO</label>
    </p>

    <input type="submit" name="save" value="Guardar"/>

    <p>
        <label for="mark-pending">Marcar como pendiente de traducir</label>
        <input id="mark-pending" type="checkbox" name="pending" value="1"/>
    </p>

</form>
<script type="text/javascript">
    $(function () {

        var items = [<?php echo implode(', ', $items); ?>];

        /* Autocomplete para elementos */
        $("#banner-project").autocomplete({
            source: items,
            minLength: 1,
            autoFocus: true,
            select: function (event, ui) {
                $("#item").val(ui.item.id);
                $("#text-banner").hide();
            }
        });

    });
</script>
