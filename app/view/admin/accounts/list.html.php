<?php
//@NOTE Julián 02/05/2013 :: quito el lanzamiento automático de filtro en los desplegables >>onchange="document.getElementById('filter-form').submit();"<<
use Goteo\Library\Text,
    Goteo\Core\View,
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
    'procStatus' => array (
        'label' => 'Estado de financiacion',
        'first' => 'Todos los estados'),
    'calls' => array (
        'label' => 'De la convocatoria',
        'first' => 'Ninguna'),
    'review' => array (
        'label' => 'Para revisión',
        'first' => 'Todos'),
    'types' => array (
        'label' => 'Extra',
        'first' => 'Todos'),
    'issue' => array (
        'label' => 'Mostrar',
        'first' => 'Todos los aportes')
); ?>
<a href="/admin/accounts/add" class="button">Generar aporte manual</a>
<a href="/admin/accounts/viewer" class="button">Visor de logs</a>&nbsp;&nbsp;&nbsp;
<?php if (!empty($filters['projects'])) : ?>
    <a href="/admin/accounts/report/<?php echo $filters['projects'] ?>#detail" class="button" target="_blank">Ver informe financiero completo del proyecto <strong><?php echo $this['projects'][$filters['projects']] ?></strong></a>
<?php endif ?>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/admin/accounts" method="get">
        <?php foreach ($the_filters as $filter=>$data) : ?>
        <div style="float:left;margin:5px;">
            <label for="<?php echo $filter ?>-filter"><?php echo $data['label'] ?></label><br />
            <select id="<?php echo $filter ?>-filter" name="<?php echo $filter ?>">
                <option value="<?php if ($filter == 'procStatus' || $filter == 'investStatus' || $filter == 'status' || $filter == 'issue') echo 'all' ?>"<?php if (($filter == 'investStatus' || $filter == 'status') && $filters[$filter] == 'all') echo ' selected="selected"'?>><?php echo $data['first'] ?></option>
            <?php foreach ($this[$filter] as $itemId=>$itemName) : ?>
                <option value="<?php echo $itemId; ?>"<?php if ($filters[$filter] === (string) $itemId) echo ' selected="selected"';?>><?php echo $itemName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <?php endforeach; ?>

        <div style="float:left;margin:5px;">
            <label for="amount-filter">Importe desde:</label><br />
            <input type="text" id ="amount-filter" name="amount" value ="<?php echo $filters['amount']?>" />
        </div>

        <div style="float:left;margin:5px;">
            <label for="amount-filter">Importe hasta:</label><br />
            <input type="text" id ="maxamount-filter" name="maxamount" value ="<?php echo $filters['maxamount']?>" />
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <label for="name-filter">Alias/Email del usuario:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>

        <div style="float:left;margin:5px;" id="date-filter-from">
            <label for="date-filter-from">Fecha desde</label><br />
            <?php echo View::get('superform/element/datebox.html.php', array('value'=>$filters['date_from'], 'id'=>'date-filter-from', 'name'=>'date_from', 'js' => true)); ?>
        </div>
        <div style="float:left;margin:5px;" id="date-filter-until">
            <label for="date-filter-until">Fecha hasta</label><br />
            <?php echo View::get('superform/element/datebox.html.php', array('value'=>$filters['date_until'], 'id'=>'date-filter-until', 'name'=>'date_until', 'js' => true)); ?>
        </div>

        <div style="float:left;margin:5px;">
            <label for="id-filter">Id:</label><br />
            <input type="text" id ="id-filter" name="id" value ="<?php echo $filters['id']?>" />
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
    <p><strong>OJO!</strong> Resultado limitado a 999 registros como máximo.</p>

    <table width="100%">
        <thead>
            <tr>
                <th></th>
                <th>Importe</th>
                <th>Fecha</th>
                <th>Cofinanciador</th>
                <th>Proyecto</th>
                <th>Metodo</th>
                <th>Estado</th>
                <th>Pool</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $invest) : ?>
            <tr>
                <td><a href="/admin/accounts/details/<?php echo $invest->id ?>" title="<?php
                    if ($invest->issue)  echo 'Incidencia! ';
                    if ($invest->anonymous == 1)  echo 'Anónimo ';
                    if ($invest->resign == 1)  echo 'Donativo ';
                    if (!empty($invest->admin)) echo 'Manual';
                    if (!empty($invest->campaign)) echo 'Riego ';
                    if (!empty($invest->droped)) echo 'Regado ('.$invest->droped.')';
                    ?>" <?php if ($invest->issue) echo ' style="color:red !important;"'; ?>>[Detalles]</a></td>
                <td><?php echo $invest->amount ?></td>
                <td><?php echo $invest->invested ?></td>
                <td><a href="mailto:<?php echo $emails[$invest->user] ?>"><?php echo $emails[$invest->user]; ?></a><a href="/admin/users/manage/<?php echo $invest->user ?>" target="_blank" title="<?php echo $this['users'][$invest->user]; ?>">[Usuario]</a></td>
                <td><a href="/admin/projects/?proj_name=<?php echo $this['projects'][$invest->project] ?>" target="_blank"><?php echo Text::recorta($this['projects'][$invest->project], 20); if (!empty($invest->campaign)) echo '<br />('.$invest->campaign.')'; ?></a></td>
                <td><?php echo $this['methods'][$invest->method] ?></td>
                <td><?php echo $this['investStatus'][$invest->investStatus] ?></td>
                <td><?php echo $invest->pool ? 'Yes' : 'No' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay transacciones que cumplan con los filtros.</p>
<?php endif;?>
</div>
