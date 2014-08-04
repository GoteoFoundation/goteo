<?php

use Goteo\Core\View;

$date = !empty($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$type = in_array($_GET['type'], array('invest', 'execute', 'daily', 'verify')) ? $_GET['type'] : 'invest';
if (!empty($_GET['date']) && !empty($_GET['type'])) {
    $showlog = true;
    if ($type == 'invest') {
        $file = GOTEO_PATH.'logs/'.str_replace('-', '', $date).'_invest.log';
    } else {
        $file = GOTEO_PATH.'logs/cron/'.str_replace('-', '', $date).'_'.$type.'.log';
    }

    if (file_exists($file)) {
        $content = file_get_contents($file);
    }

} else {
    $showlog = false;
}
?>
<div class="widget">
    <h3>Seleccionar log por tipo y fecha</h3>
    <form id="filter-form" action="/admin/accounts/viewer" method="get">
        <div style="float:left;margin:5px;">
            <label for="type-filter">Tipo de proceso:</label><br />
            <select id="type-filter" name="type">
                <option value="invest"<?php if ($type == 'invest') echo ' selected="selected"';?>>Aportes</option>
                <option value="execute"<?php if ($type == 'execute') echo ' selected="selected"';?>>Cargos</option>
                <option value="verify"<?php if ($type == 'verify') echo ' selected="selected"';?>>Verificaciones</option>
                <option value="daily"<?php if ($type == 'daily') echo ' selected="selected"';?>>Avisos</option>
            </select>
        </div>
        <div style="float:left;margin:5px;" id="hdate">
            <label for="hdate">Fecha del log:</label><br />
            <?php echo new View('library/superform/view/element/datebox.html.php', array('value'=>$date, 'id'=>'hdate', 'name'=>'date')); ?>
        </div>
        <div style="float:left;margin:5px;">
            <input type="submit" value="Ver" />
        </div>
    </form>
</div>

<?php if ($showlog) echo '<strong>archivo:</strong> ' . $file . '<br /><br />';
if (!empty($content)) echo nl2br($content); else echo 'No encontrado'; ?>
<br /><br /><br />
