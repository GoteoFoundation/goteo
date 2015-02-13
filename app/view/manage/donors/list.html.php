<?php

use Goteo\Library\Text;

//$data = $this['data'];
$filters = $this['filters'];
?>
<div class="widget board">
    <form id="filter-form" action="/manage/donors/excel" method="get" target="_blank">

        <div style="float:left;margin:5px;">
            <label for="year-filter">A&ntilde;o aporte:</label><br />
            <select id ="year-filter" name="year">
                <option value=""<?php if (empty($filters['year'])) echo ' selected="selected"'; ?>>Todos</option>
                <option value="2013"<?php if ($filters['year']=='2013') echo ' selected="selected"'; ?>>Archivo (2013)</option>
                <option value="2014"<?php if ($filters['year']=='2014') echo ' selected="selected"'; ?>>Anterior (2014)</option>
                <option value="2015"<?php if ($filters['year']=='2015') echo ' selected="selected"'; ?>>Actual (2015)</option>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="status-filter">Estado datos:</label><br />
            <select id ="status-filter" name="status">
                <option value=""<?php if ($filters['status']=='') echo ' selected="selected"'; ?>>Todos</option>
                <option value="pending"<?php if ($filters['status']=='pending') echo ' selected="selected"'; ?>>Pendientes de revision</option>
                <option value="edited"<?php if ($filters['status']=='edited') echo ' selected="selected"'; ?>>Revisados pero no confirmados</option>
                <option value="confirmed"<?php if ($filters['status']=='confirmed') echo ' selected="selected"'; ?>>Confirmados</option>
                <option value="emited"<?php if ($filters['status']=='emited') echo ' selected="selected"'; ?>>Certificado emitido</option>
                <option value="notemited"<?php if ($filters['status']=='notemited') echo ' selected="selected"'; ?>>Confirmado pero no emitido</option>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="user-filter">Usuario (id/alias/email):</label><br />
            <input id="user-filter" name="user" value="<?php echo $filters['user']; ?>" />
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="Sacar datos" />
        </div>
    </form>
</div>