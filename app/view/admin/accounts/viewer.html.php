<?php

use Goteo\Core\View;

$type = $vars['type'];
$date = $vars['date'];

?>
<div class="widget">
    <h3>Seleccionar log por tipo y fecha</h3>
    <form id="filter-form" action="/admin/accounts/viewer" method="get">
        <div style="float:left;margin:5px;">
            <label for="type-filter">Tipo de proceso:</label><br />
            <select id="type-filter" name="type">
                <option value="log"<?php if ($type == 'log') echo ' selected="selected"';?>>--</option>
                <option value="execute"<?php if ($type == 'execute') echo ' selected="selected"';?>>Cargos</option>
                <option value="verify"<?php if ($type == 'verify') echo ' selected="selected"';?>>Verificaciones</option>
                <option value="daily"<?php if ($type == 'daily') echo ' selected="selected"';?>>Avisos</option>
            </select>
        </div>
        <div style="float:left;margin:5px;" id="hdate">
            <label for="hdate">Fecha del log:</label><br />
            <?php echo View::get('superform/element/datebox.html.php', array('value'=>$date, 'id'=>'hdate', 'name'=>'date', 'js' => true)); ?>
        </div>
        <div style="float:left;margin:5px;">
            <input type="submit" value="Ver" />
        </div>
    </form>
</div>
<div style="width:780px; height:1000px; overflow: scroll;">
    <?php echo $vars['content']; ?>
</div>
