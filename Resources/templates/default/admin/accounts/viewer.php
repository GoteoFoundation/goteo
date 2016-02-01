<?php

use Goteo\Core\View;

$type = $this->type;
$date = $this->date;

?>
<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

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
            <?= $this->html('input', ['value' => $date, 'name' => 'date', 'attribs' => ['id'=>'hdate', 'class' => 'datepicker']]) ?>
        </div>
        <div style="float:left;margin:5px;">
            <input type="submit" value="Ver" />
        </div>
    </form>
</div>
<div style="width:780px; height:1000px; overflow: scroll;">
    <?= $this->content ?>
</div>

<?php $this->replace() ?>
