<?php
use Goteo\Library\Text;

/*
 * TODO
 *
    <a href="/admin/accounting/details/<?php echo $invest->id; ?>">[Detalles]</a>
    <a href="/admin/accounting/execute/<?php echo $invest->id; ?>">[Ejecutar]</a>
 */

$filters = $this['filters'];

?>
<!-- filtros -->
<?php $the_filters = array(
    'projects' => array (
        'label' => 'Proyecto',
        'first' => 'Todos los proyectos'),
    'users' => array (
        'label' => 'Usuario',
        'first' => 'Todos los usuarios'),
    'methods' => array (
        'label' => 'Método de pago',
        'first' => 'Todos los métodos'),
    'status' => array (
        'label' => 'Estado de proyecto',
        'first' => 'Todos los estados'),
    'investStatus' => array (
        'label' => 'Estado de aporte',
        'first' => 'Todos los estados'),
    'campaigns' => array (
        'label' => 'Convocatoria',
        'first' => 'Todas las convocatorias'),
    'types' => array (
        'label' => 'Extra',
        'first' => 'Todos')
); ?>
<a href="/admin/invests/add" class="button red">Generar aportes manualmente</a>&nbsp;&nbsp;&nbsp;
<?php if (!empty($filters['projects'])) : ?>
    <a href="/admin/invests/report/<?php echo $filters['projects'] ?>" class="button red">Informe del proyecto filtrado</a>
<?php endif ?>
<div class="widget board">
    <h3 class="title">Filtros</h3>
    <form id="filter-form" action="/admin/invests" method="get">
        <input type="hidden" name="filtered" value="yes" />
        <?php foreach ($the_filters as $filter=>$data) : ?>
        <div style="float:left;margin:5px;">
            <label for="<?php echo $filter ?>-filter"><?php echo $data['label'] ?></label><br />
            <select id="<?php echo $filter ?>-filter" name="<?php echo $filter ?>" onchange="document.getElementById('filter-form').submit();">
                <option value="<?php if ($filter == 'investStatus') echo 'all' ?>"<?php if ($filter == 'investStatus' && $filters[$filter] == 'all') echo ' selected="selected"'?>><?php echo $data['first'] ?></option>
            <?php foreach ($this[$filter] as $itemId=>$itemName) : ?>
                <option value="<?php echo $itemId; ?>"<?php if ($filters[$filter] === (string) $itemId) echo ' selected="selected"';?>><?php echo $itemName; ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <?php endforeach; ?>
    </form>
    <br clear="both" />
    <a href="/admin/invests">Quitar filtros</a>
</div>

<div class="widget board">
<?php if (empty($filters)) : ?>
    <p>Filtra algun criterio</p>
<?php elseif (!empty($this['list'])) : ?>
    <table width="100%">
        <thead>
            <tr>
                <th></th>
                <th>Aporte ID</th>
                <th>Fecha</th>
                <th>Cofinanciador</th>
                <th>Proyecto</th>
                <th>Estado</th>
                <th>Metodo</th>
                <th>Estado aporte</th>
                <th>Importe</th>
                <th>Extra</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['list'] as $invest) : ?>
            <tr>
                <td><a href="/admin/invests/details/<?php echo $invest->id ?>">[Detalles]</a></td>
                <td><?php echo $invest->id ?></td>
                <td><?php echo $invest->invested ?></td>
                <td><?php echo $this['users'][$invest->user] ?></td>
                <td><?php echo $this['projects'][$invest->project]; if (!empty($invest->campaign)) echo '<br />('.$this['campaigns'][$invest->campaign].')'; ?></td>
                <td><?php echo $this['status'][$invest->status] ?></td>
                <td><?php echo $this['methods'][$invest->method] ?></td>
                <td><?php echo $this['investStatus'][$invest->investStatus] ?></td>
                <td><?php echo $invest->amount ?></td>
                <td><?php echo $invest->charged ?></td>
                <td><?php echo $invest->returned ?></td>
                <td>
                    <?php if ($invest->anonymous == 1)  echo 'Anónimo ' ?>
                    <?php if ($invest->resign == 1)  echo 'Donativo ' ?>
                    <?php if (!empty($invest->admin)) echo 'Manual' ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
<?php else : ?>
    <p>No hay aportes que cumplan con los filtros.</p>
<?php endif;?>
</div>