<?php

use Goteo\Library\Text;

$reports = $this['reports'];
$filters = $this['filters'];
$data    = $this['data'];
?>
<a href="/admin/reports/donors" class="button">Donantes</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/reports/projects" class="button">Impulsores</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/reports/paypal" class="button">Estado actual PayPal</a>

<div class="widget board">
    <form id="filter-form" action="/admin/reports" method="get">

        <div style="float:left;margin:5px;">
            <label for="report-filter">Informe</label><br />
            <select id ="report-filter" name="report">
                <option value="">Seleccionar informe</option>
                <?php foreach ($reports as $repId=>$repName) :
                    $selected = ($repId == $filters['report']) ? ' selected="selected"' : '';
                    ?>
                <option value="<?php echo $repId; ?>"<?php echo $selected; ?>><?php echo $repName; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!--
        <div style="float:left;margin:5px;">
            <label for="date-filter-from">Fecha desde</label><br />
            <input type="text" id ="date-filter-from" name="from" value ="<?php echo $filters['from']?>" />
        </div>

        <div style="float:left;margin:5px;">
            <label for="date-filter-until">Fecha hasta</label><br />
            <input type="text" id ="date-filter-until" name="until" value ="<?php echo $filters['until']?>" />
        </div>
        -->

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="Ver" />
        </div>
    </form>
</div>

<div class="widget board">
<?php if (!empty($data)) : ?>
    
    <table>
        <thead>
            <tr>
                <td></td>
                <?php foreach ($data['columns'] as $column) : ?>
                <th><?php echo $column; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['rows'] as $i=>$label) : ?>
            <tr>
                <th style="text-align: left;"><?php echo $label; ?></th>
                <?php foreach ($data['data'][$i] as $value) : ?>
                <td style="text-align: right;"><?php echo $value; ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>