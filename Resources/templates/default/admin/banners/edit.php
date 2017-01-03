<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>
<?php
$banner = $this->banner;

//para autocomplete
$items = array();
$node = $this->admin_node;
if ($this->is_master_node($node)) {
    // proyectos disponibles
    // si tenemos ya proyecto seleccionado lo incluimos
    $projects = \Goteo\Model\Banner::available($banner->project);
    foreach ($projects as $project) {
        $items[] = '{ value: "' . str_replace('"', '\"', $project->name) . '", id: "' . $project->id . '" }';
    }
    $status = \Goteo\Model\Project::status();

    $image_size_txt = '700 x 156 (estricto)';
} else {
    $image_size_txt = '940 x 270 (estricto)';
}


?>
<div class="widget">
<form method="post" action="/admin/banners/<?= $banner->id ? $this->action.'/'.$banner->id : $this->action ?>" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?= $this->action ?>"/>
    <input type="hidden" name="order" value="<?= $banner->order ?>"/>
    <input type="hidden" name="id" value="<?= $banner->id ?>"/>
    <input type="hidden" id="item" name="item" value="<?= $banner->project ?>"/>

    <?php if ($this->is_master_node($node)) : ?>

        <p>
            <label for="banner-project">Proyecto: (autocomplete nombre)</label><br/>
            <input type="text" name="project" id="banner-project" value="<?= $banner->name ?>" size="60"/>
        </p>

        <script type="text/javascript">
        // @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

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

        // @license-end
        </script>
    <?php endif ?>

    <div id="text-banner"<?php if (!empty($banner->project)) echo ' style="display: none;"' ?>>
        <p>
            <label for="banner-name">T&iacute;tulo:</label><br/>
            <input type="text" name="title" id="banner-title" value="<?= $banner->title ?>" size="50"/>
        </p>

        <p>
            <label for="banner-description">Descripci&oacute;n:</label><br/>
            <input type="text" name="description" id="banner-description" value="<?= $banner->description ?>"
                   size="85"/>
        </p>

        <p>
            <label for="banner-url">Enlace:</label><br/>
            <input type="text" name="url" id="banner-url" value="<?= $banner->url ?>" size="85"/>
        </p>
    </div>

    <p>
        <label for="banner-image">Imagen de fondo: <?= $image_size_txt ?></label><br/>
        <input type="file" id="banner-image" name="image"/>
        <?php if (!empty($banner->image)) : ?>
            <br/>
            <input type="hidden" name="prev_image" value="<?= $banner->image->id ?>"/>
            <img src="<?= $banner->image->getLink(700, 150, true) ?>" title="Fondo banner" alt="falta imagen"/>
            <input type="submit" name="image-<?= $banner->image->hash ?>-remove" value="Quitar" />
        <?php endif ?>
    </p>

    <p>
        <label>Publicado:</label><br/>
        <label><input type="radio" name="active" id="banner-active"
                      value="1"<?php if ($banner->active) echo ' checked="checked"' ?>/> S&Iacute;</label>
        &nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="active" id="banner-inactive"
                      value="0"<?php if (!$banner->active) echo ' checked="checked"' ?>/> NO</label>
    </p>

    <input type="submit" name="save" value="Guardar"/>

    <p>
        <label for="mark-pending">Marcar como pendiente de traducir</label>
        <input id="mark-pending" type="checkbox" name="pending" value="1"/>
    </p>

</form>
</div>
<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function () {

        var items = [<?= implode(', ', $items) ?>];

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
// @license-end
</script>
<?php $this->append() ?>

