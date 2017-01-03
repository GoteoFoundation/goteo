<?php

use Goteo\Library\Text;

?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

jQuery(document).ready(function ($) {

    $('#criteria-section').change(function () {
        order = $.ajax({async: false, url: '/ws/get_criteria_order/'+$('#criteria-section').val()}).responseText;
        $('#criteria-order').val(order);
        $('#criteria-num').html(order);
    });

});
// @license-end
</script>

<div class="widget board">
    <form method="post" action="/admin/criteria">

        <input type="hidden" name="action" value="<?php echo $vars['action']; ?>" />
        <input type="hidden" name="id" value="<?php echo $vars['criteria']->id; ?>" />

        <p>
        <?php if ($vars['action'] == 'add') : ?>
            <label for="criteria-section">Sección:</label><br />
            <select id="criteria-section" name="section">
                <option value="" disabled>Elige la sección</option>
                <?php foreach ($vars['sections'] as $id=>$name) : ?>
                <option value="<?php echo $id; ?>"<?php if ($id == $vars['criteria']->section) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
        <?php else : ?>
            <label for="criteria-section">Sección: <?php echo $vars['sections'][$vars['criteria']->section]; ?></label><br />
            <input type="hidden" name="section" value="<?php echo $vars['criteria']->section; ?>" />
        <?php endif; ?>
        </p>

        <p>
            <label for="criteria-title">Título:</label><br />
            <input type="text" name="title" id="criteria-title" value="<?php echo $vars['criteria']->title; ?>" />
        </p>

        <p>
            <label for="criteria-description">Descripción:</label><br />
            <textarea name="description" id="criteria-description" cols="60" rows="10"><?php echo $vars['criteria']->description; ?></textarea>
        </p>

        <p>
            <label for="criteria-order">Posición:</label><br />
            <select name="move">
                <option value="same" selected="selected" disabled>Tal cual</option>
                <option value="up">Antes de </option>
                <option value="down">Después de </option>
            </select>&nbsp;
            <input type="text" name="order" id="criteria-order" value="<?php echo $vars['criteria']->order; ?>" size="4" />
            &nbsp;de&nbsp;<span id="criteria-num"><?php echo $vars['criteria']->cuantos; ?></span>
        </p>


        <input type="submit" name="save" value="Guardar" />

        <p>
            <label for="mark-pending">Marcar como pendiente de traducir</label>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </p>

    </form>
</div>
