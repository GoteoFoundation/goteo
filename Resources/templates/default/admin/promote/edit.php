<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$promo = $this->promo;

// TODO: better autocomplete
// proyectos disponibles
// si tenemos ya proyecto seleccionado lo incluimos
$projects = $this->projects;
$action = $this->action;
//para autocomplete
$items = array();

foreach ($projects as $project) {
    $items[] = '{ value: "'.str_replace('"','\"',$project->name).'", id: "'.$project->id.'" }';
        if($promo->project === $project->id) $preval=$project->name;
}
?>
<div class="widget board">
    <form method="post" action="<?php echo $action ?>">
        <input type="hidden" name="order" value="<?php echo $promo->order ?>" />
        <input type="hidden" id="item" name="item" value="<?php echo $promo->project; ?>" />

    <div>
        <label for="projects-filter">Proyecto: (autocomplete nombre)</label><br />
        <input type="text" name="project" id="projects-filter" value="<?php echo $preval;?>" size="60" />
    </div>


    <?php if ($this->titleAndDesc) : ?>

        <p>
            <label for="promo-name">Título:</label><span style="font-style:italic;">Máximo 24 caracteres</span><br />
            <input type="text" name="title" id="promo-title" value="<?php echo $promo->title; ?>" maxlength="24" style="width:500px;" />
        </p>

        <p>
            <label for="promo-description">Descripción:</label><span style="font-style:italic;">Máximo 100 caracteres</span><br />
            <input type="text" name="description" id="promo-description" maxlength="100" value="<?php echo $promo->description; ?>" style="width:750px;" />
        </p>
        <p>
            <label for="mark-pending">Marcar como pendiente de traducir</label>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </p>

    <?php endif; ?>

    <p>
        <label>Publicado:</label><br />
        <label><input type="radio" name="active" id="promo-active" value="1"<?php if ($promo->active) echo ' checked="checked"'; ?>/> SÍ</label>
        &nbsp;&nbsp;&nbsp;
        <label><input type="radio" name="active" id="promo-inactive" value="0"<?php if (!$promo->active) echo ' checked="checked"'; ?>/> NO</label>
    </p>

        <input type="submit" name="save" value="Guardar" />

    </form>
</div>
<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function () {

    var items = [<?php echo implode(', ', $items); ?>];

    /* Autocomplete para elementos */
    $( "#projects-filter" ).autocomplete({
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

<?php $this->append() ?>
