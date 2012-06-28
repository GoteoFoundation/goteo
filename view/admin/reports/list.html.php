<?php

use Goteo\Library\Text;

// paginacion
require_once 'library/pagination/pagination.php';

$filters = $this['filters'];
$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters .= "&{$key}={$value}";
}

$pagedResults = new \Paginated($this['reports'], 20, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<a href="/admin/reports/paypal" class="button">PayPal</a>

<div class="widget board">
    <form id="filter-form" action="/admin/reports" method="get">

        <div style="float:left;margin:5px;">
            <label for="date-filter-from">Fecha desde</label><br />
            <input type="text" id ="date-filter-from" name="from" value ="<?php echo $filters['from']?>" />
        </div>
        <div style="float:left;margin:5px;">
            <label for="date-filter-until">Fecha hasta</label><br />
            <input type="text" id ="date-filter-until" name="until" value ="<?php echo $filters['until']?>" />
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="filtrar" />
        </div>
    </form>
</div>

<div class="widget board">
<?php /*if ($filters['filtered'] != 'yes') : ?>
    <p>Es necesario poner algun filtro</p>
<?php else*/ 

    if (!empty($this['reports'])) : ?>
    
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Dato</th>
                <th>Dato</th>
                <th>Dato</th>
                <th>Dato</th>
                <th>Dato</th>
                <th>Dato</th>
                <th>Dato</th>
                <th>Dato</th>
                <th>Dato</th>
                <th>Dato</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($report = $pagedResults->fetchPagedRow()) : ?>
            <tr>
                <td><?php echo $report->fecha; ?></td>
                <td><?php echo $report->data; ?></td>
                <td><?php echo $report->data; ?></td>
                <td><?php echo $report->data; ?></td>
                <td><?php echo $report->data; ?></td>
                <td><?php echo $report->data; ?></td>
                <td><?php echo $report->data; ?></td>
                <td><?php echo $report->data; ?></td>
                <td><?php echo $report->data; ?></td>
                <td><?php echo $report->data; ?></td>
                <td><?php echo $report->data; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<ul id="pagination">
<?php   $pagedResults->setLayout(new DoubleBarLayout());
        echo $pagedResults->fetchPagedNavigation(str_replace('?', '&', $the_filters)); ?>
</ul>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>