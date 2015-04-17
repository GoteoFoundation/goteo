<?php

use Goteo\Library\Text,
    Goteo\Model;

$promo = $vars['promo'];

$items = array();
// $iId = id de la recompensa
// $iObj = datos: recompensa, proyecto, importe
//@TODO montar identificador idRecompensa¬idProyecto¬importe
//@TODO montar valor: nomReco(50)+importe€+nomProj(50)
foreach ($vars['items'] as $iId=>$iObj) {
    $el_val = str_replace(array("'", '"'), '`', $iObj->name).' ['.$iObj->icon.' '.$iObj->amount.'€] ('.str_replace(array("'", '"'), '`', $iObj->projname).')';
    $el_id = $iObj->reward.'¬'.$iObj->project.'¬'.$iObj->amount;
    $items[] = '{ value: "'.$el_val.')", id: "'.$el_id.'" }';
    if ($iId == $promo->reward) $preVal = "$el_val";
}
?>
<form method="post" action="/admin/bazar" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $vars['action'] ?>" />
    <input type="hidden" name="order" value="<?php echo $promo->order ?>" />
    <input type="hidden" name="id" value="<?php echo $promo->id; ?>" />

    <input type="hidden" id="item" name="item" value="<?php echo $promo->reward.'¬'.$promo->project->id.'¬'.$promo->amount; ?>" />

    <div class="ui-widget">
        <label for="busca-item">Buscador recompensa:</label><br />
        <input type="text" id="busca-item" name="item_searcher" value="<?php echo $preVal; ?>" style="width:500px;"/>
    </div>

    <br />

<p>
    <label for="promo-name">Título:</label><br />
    <input type="text" name="title" id="promo-title" value="<?php echo $promo->title; ?>" style="width:750px;" />
</p>

<p>
    <label for="promo-description">Descripción:</label><br />
    <input type="text" name="description" id="promo-description" value="<?php echo $promo->description; ?>" style="width:750px;" />
</p>

<p>
    <label for="promo-image">Imagen del regalo:</label><br />
        <input type="hidden" name="prev_image" value="<?php echo $promo->image->id ?>" />
    <?php if ($promo->image instanceof Model\Image) : ?>
        <img src="<?php echo SRC_URL.'/images/'.$promo->image->name; ?>" title="Imagen regalo" alt="falta imagen"/>
        <input type="submit" name="image-<?php echo $promo->image->hash; ?>-remove" value="Quitar" />
        <br />
    <?php endif; ?>
    <input type="file" id="promo-image" name="image" />
</p>

<p>
    <label>Publicado:</label><br />
    <label><input type="radio" name="active" id="promo-active" value="1"<?php if ($promo->active) echo ' checked="checked"'; ?>/> SÍ</label>
    &nbsp;&nbsp;&nbsp;
    <label><input type="radio" name="active" id="promo-inactive" value="0"<?php if (!$promo->active) echo ' checked="checked"'; ?>/> NO</label>
</p>

    <input type="submit" name="save" value="Guardar" />

    <p>
        <label for="mark-pending">Marcar como pendiente de traducir</label>
        <input id="mark-pending" type="checkbox" name="pending" value="1" />
    </p>

</form>
<script type="text/javascript">
$(function () {

    var items = [<?php echo implode(', ', $items); ?>];

    /* Autocomplete para elementos */
    $( "#busca-item" ).autocomplete({
      source: items,
      minLength: 2,
      autoFocus: true,
      select: function( event, ui) {
                $("#item").val(ui.item.id);
            }
    });

});
</script>
