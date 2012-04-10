<?php
use Goteo\Library\Text,
    Goteo\Model\Invest;

$filters = $this['filters'];

$emails = Invest::emails(true);
?>
<!-- filtros -->
<?php $the_filters = array(
    'projects' => array (
        'label' => 'Proyecto',
        'first' => 'Todos los proyectos'),
    'methods' => array (
        'label' => 'Método de pago',
        'first' => 'Todos los métodos'),
    'investStatus' => array (
        'label' => 'Estado del aporte',
        'first' => 'Todos los estados'),
    'investStatus' => array (
        'label' => 'Estado de aporte',
        'first' => 'Todos los estados'),
    'calls' => array (
        'label' => 'De la convocatoria',
        'first' => 'Ninguna'),
    'review' => array (
        'label' => 'Para revisión',
        'first' => 'Todos'),
    'types' => array (
        'label' => 'Extra',
        'first' => 'Todos')
); ?>
<a href="/admin/accounts/add" class="button weak">Generar aporte manual</a>
<a href="http://ppcalc.com/es" target="_blank" class="button">Calculadora PayPal</a>&nbsp;&nbsp;&nbsp;
<a href="/cron/execute" target="_blank" class="button red">Ejecutar cargos</a>&nbsp;&nbsp;&nbsp;
<a href="/cron/verify" target="_blank" class="button red">Verificar preapprovals</a>&nbsp;&nbsp;&nbsp;
<a href="/admin/accounts/viewer" class="button">Visor de logs</a>&nbsp;&nbsp;&nbsp;
<?php if (!empty($filters['projects'])) : ?>
    <br />
    <a href="/admin/accounts/report/<?php echo $filters['projects'] ?>#detail" class="button" target="_blank">Informe financiero completo de <?php echo $this['projects'][$filters['projects']] ?></a>&nbsp;&nbsp;&nbsp;
    <a href="/cron/dopay/<?php echo $filters['projects'] ?>" target="_blank" class="button red" onclick="return confirm('No hay vuelta atrás, ok?');">Realizar pagos secundarios a <?php echo $this['projects'][$filters['projects']] ?></a>
<?php endif ?>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/admin/accounts" method="get">
        <?php foreach ($the_filters as $filter=>$data) : ?>
        <div style="float:left;margin:5px;">
            <label for="<?php echo $filter ?>-filter"><?php echo $data['label'] ?></label><br />
            <select id="<?php echo $filter ?>-filter" name="<?php echo $filter ?>" onchange="document.getElementById('filter-form').submit();">
                <option value="<?php if ($filter == 'investStatus' || $filter == 'status') echo 'all' ?>"<?php if (($filter == 'investStatus' || $filter == 'status') && $filters[$filter] == 'all') echo ' selected="selected"'?>><?php echo $data['first'] ?></option>
            <?php foreach ($this[$filter] as $itemId=>$itemName) : ?>
                <option value="<?php echo $itemId; ?>"<?php if ($filters[$filter] === (string) $itemId) echo ' selected="selected"';?>><?php echo $itemName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <?php endforeach; ?>
        <br clear="both" />
        
        <div style="float:left;margin:5px;">
            <label for="name-filter">Alias/Email del usuario:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>

        <div style="float:left;margin:5px;">
            <label for="date-filter-from">Fecha desde</label><br />
            <input type="text" id ="date-filter-from" name="date_from" value ="<?php echo $filters['date_from']?>" />
        </div>
        <div style="float:left;margin:5px;">
            <label for="date-filter-until">Fecha hasta</label><br />
            <input type="text" id ="date-filter-until" name="date_until" value ="<?php echo $filters['date_until']?>" />
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/accounts/?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro, hay demasiados registros!</p>
<?php elseif (!empty($this['list'])) : ?>
<?php $Total = 0; foreach ($this['list'] as $invest) { $Total += $invest->amount; } ?>
    <p><strong>TOTAL:</strong>  <?php echo number_format($Total, 0, '', '.') ?> &euro;</p>
    
    <table width="100%">
        <thead>
            <tr>
                <th></th>
                <th>Aporte ID</th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Cofinanciador</th>
                <th></th>
                <th>Proyecto</th>
                <th>Metodo</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $invest) : ?>
            <tr>
                <td><a href="/admin/accounts/details/<?php echo $invest->id ?>" title="<?php
                    if ($invest->anonymous == 1)  echo 'Anónimo ';
                    if ($invest->resign == 1)  echo 'Donativo ';
                    if (!empty($invest->admin)) echo 'Manual';
                    if (!empty($invest->campaign)) echo 'Riego ';
                    if (!empty($invest->droped)) echo 'Regado (<strong>'.$invest->droped.'</strong>)';
                    ?>">[Detalles]</a></td>
                <td><?php echo $invest->id ?></td>
                <td><?php echo $invest->amount ?></td>
                <td><?php echo $invest->invested ?></td>
                <td><a href="/admin/users/manage/<?php echo $invest->user ?>" target="_blank"><?php echo $this['users'][$invest->user]; ?></a></td>
                <td><?php echo $emails[$invest->user]; ?></td>
                <td><a href="/admin/projects/?name=<?php echo $this['projects'][$invest->project] ?>" target="_blank"><?php echo $this['projects'][$invest->project]; if (!empty($invest->campaign)) echo '<br />('.$this['campaigns'][$invest->campaign].')'; ?></a></td>
                <td><?php echo $this['methods'][$invest->method] ?></td>
                <td><?php echo $this['investStatus'][$invest->investStatus] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay transacciones que cumplan con los filtros.</p>
<?php endif;?>
</div>