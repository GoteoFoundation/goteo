<?php

use Goteo\Library\Text;

$data = $vars['data'];
$filters = $vars['filters'];
?>
<div class="widget board">
    <form id="filter-form" action="/admin/reports/top" method="get">

        <div style="float:left;margin:5px;">
            <label for="top-filter">Ver por:</label><br />
            <select id ="top-filter" name="top">
                <option value="numproj"<?php if ($filters['top']=='numproj') echo ' selected="selected"'; ?>>Proyectos</option>
                <option value="amount"<?php if ($filters['top']=='amount') echo ' selected="selected"'; ?>>Cantidad</option>
            </select>
        </div>

        <div style="float:left;margin:5px;">
            <label for="limit-filter">Cuantos:</label><br />
            <select id ="limit-filter" name="limit">
                <option value="10"<?php if ($filters['limit']=='10') echo ' selected="selected"'; ?>>10</option>
                <option value="25"<?php if ($filters['limit']=='25') echo ' selected="selected"'; ?>>25</option>
                <option value="50"<?php if ($filters['limit']=='50') echo ' selected="selected"'; ?>>50</option>
                <option value="100"<?php if ($filters['limit']=='100') echo ' selected="selected"'; ?>>100</option>
                <option value="300"<?php if ($filters['limit']=='300') echo ' selected="selected"'; ?>>300</option>
            </select>
        </div>

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
                <th>Nombre</th>
                <th>Id</th>
                <th>Email</th>
                <th>Cantidad</th>
                <th>Proyectos</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row) : ?>
            <tr>
                <td><a href="/admin/users/?id=<?php echo $row->id; ?>"><?php echo $row->name; ?></a></td>
                <td><a href="/admin/users/manage/<?php echo $row->id; ?>"><?php echo $row->id; ?></a></td>
                <td><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></td>
                <td><?php echo $row->amount; ?></td>
                <td><a href="/admin/accounts/?name=<?php echo $row->email; ?>"><?php echo $row->numproj; ?></a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>
