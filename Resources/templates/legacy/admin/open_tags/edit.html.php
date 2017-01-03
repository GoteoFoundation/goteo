<?php

use Goteo\Library\Text,
    Goteo\Model;

$open_tag = $vars['open_tag'];
$items = array();
$node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;
// $iId = id del post
// $iObj = titulo
foreach ($vars['items'] as $iId=>$iObj) {
    $el_val = str_replace(array("'", '"'), '`', $iObj)." ({$iId})";
    $items[] = '{ value: "'.$el_val.'", id: "'.$iId.'" }';
    if ($iId == $open_tag->post) $preVal = "$el_val";
}

?>
<form method="post" action="/admin/open_tags/<?php echo $vars['action'] ?>" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $vars['action'] ?>" />
    <!--<input type="hidden" name="order" value="<?php echo $story->order ?>" />-->
    <input type="hidden" name="id" value="<?php echo $open_tag->id; ?>" />

    <p>
        <label for="open-tag-name">Nombre:</label><br />
        <input type="text" name="name" id="open-tag-name" value="<?php echo $open_tag->name; ?>" size="60" />
    </p>
    <p>
    <label for="open-tag-description">Descripción</label><br />
    <textarea id="open-tag-description" name="description" cols="60" rows="2"><?php echo $open_tag->description; ?></textarea>
    </p>

    <input type="hidden" id="item" name="item" value="<?php echo $open_tag->post; ?>" />

    <div id="text-open-tag">
        <p>
            <label for="open-tag-post">Post: (autocomplete título o numero)</label><br />
            <input type="text" name="post" id="open-tag-post" value="<?php echo $preVal; ?>" size="60" />
        </p>
    </div>


    <input type="submit" name="save" value="Guardar" />

    <p>
        <label for="mark-pending">Marcar como pendiente de traducir</label>
        <input id="mark-pending" type="checkbox" name="pending" value="1" />
    </p>

</form>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function () {

    var items = [<?php echo implode(', ', $items); ?>];

    /* Autocomplete para elementos */
    $( "#open-tag-post" ).autocomplete({
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
