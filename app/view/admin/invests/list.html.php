<?php
//@NOTE Julián 02/05/2013 :: quito el lanzamiento automático de filtro en los desplegables >>onchange="document.getElementById('filter-form').submit();"<<
use Goteo\Library\Text,
    Goteo\Model\Invest;

$filters = $vars['filters'];
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
        'label' => 'Estado de aporte',
        'first' => 'Todos los estados'),
    'calls' => array (
        'label' => 'De la convocatoria',
        'first' => 'Ninguna'),
    'types' => array (
        'label' => 'Extra',
        'first' => 'Todos')
); ?>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/admin/invests" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <?php foreach ($the_filters as $filter=>$data) : ?>
        <div style="float:left;margin:5px;">
            <label for="<?php echo $filter ?>-filter"><?php echo $data['label'] ?></label><br />
            <select id="<?php echo $filter ?>-filter" name="<?php echo $filter ?>">
                <option value="<?php if ($filter == 'investStatus' || $filter == 'status') echo 'all' ?>"<?php if (($filter == 'investStatus' || $filter == 'status') && $filters[$filter] == 'all') echo ' selected="selected"'?>><?php echo $data['first'] ?></option>
            <?php foreach ($vars[$filter] as $itemId=>$itemName) : ?>
                <option value="<?php echo $itemId; ?>"<?php if ($filters[$filter] === (string) $itemId) echo ' selected="selected"';?>><?php echo substr($itemName, 0, 50); ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <?php endforeach; ?>
        <br clear="both" />

        <div style="float:left;margin:5px;">
            <label for="name-filter">Alias/Email del usuario:</label><br />
            <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
    <br clear="both" />
    <a href="/admin/invests?reset=filters">Quitar filtros</a>
</div>

<div class="widget board">
<?php if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro, hay demasiados registros!</p>
<?php elseif (!empty($vars['list'])) : ?>
<?php $Total = 0; foreach ($vars['list'] as $invest) { $Total += $invest->amount; } ?>
    <p><strong>TOTAL:</strong>  <?php echo number_format($Total, 0, '', '.') ?> &euro;</p>
    <p><strong>OJO!</strong> Resultado limitado a 999 registros como máximo.</p>

    <table width="100%">
        <thead>
            <tr>
                <th></th>
                <th>Aporte ID</th>
                <th>Fecha</th>
                <th>Cofinanciador</th>
                <th>Proyecto</th>
                <th>Metodo</th>
                <th>Estado</th>
                <th>Importe</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['list'] as $invest) : ?>
            <tr>
                <td><a href="/admin/invests/details/<?php echo $invest->id ?>" title="<?php
                    if ($invest->anonymous == 1)  echo 'Anónimo ';
                    if ($invest->resign == 1)  echo 'Donativo ';
                    if (!empty($invest->admin)) echo 'Manual ';
                    if (!empty($invest->campaign)) echo 'Riego ';
                    if (!empty($invest->droped)) echo 'Regado (<strong>'.$invest->droped.'</strong>)';
                   ?>">[Detalles]</a></td>
                <td><?php echo $invest->id ?></td>
                <td><?php echo $invest->invested ?></td>
                <td><a href="/admin/users?id=<?php echo $invest->user ?>" target="_blank"><?php echo $vars['users'][$invest->user]; ?></a><?php if (!empty($invest->call)) echo '<br />(<strong>'.$vars['calls'][$invest->call].'</strong>)'; ?></td>
                <td><a href="/admin/projects?name=<?php echo $vars['projects'][$invest->project] ?>" target="_blank"><?php echo $vars['projects'][$invest->project] ?></a></td>
                <td><?php echo $vars['methods'][$invest->method] ?></td>
                <td><?php echo $vars['investStatus'][$invest->investStatus] ?></td>
                <td><?php echo $invest->amount ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay aportes que cumplan con los filtros.</p>
<?php endif;?>
</div>
