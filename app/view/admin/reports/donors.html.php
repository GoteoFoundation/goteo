<?php

use Goteo\Library\Text;

$data = $vars['data'];
$filters = $vars['filters'];
?>
<a href="/sacaexcel/donors/?year=<?php echo $filters['year']; ?>&status=<?php echo $filters['status']; ?>&user=<?php echo $filters['user']; ?>" target="_blank">Sacaexcel</a>
<div class="widget board">
    <form id="filter-form" action="/admin/reports/donors" method="get">

        <div style="float:left;margin:5px;">
            <label for="year-filter">A&ntilde;o fiscal:</label><br />
            <select id ="year-filter" name="year">
                <option value="2012"<?php if ($filters['year']=='2012') echo ' selected="selected"'; ?>>Hasta 2012</option>
                <option value="2013"<?php if ($filters['year']=='2013') echo ' selected="selected"'; ?>>2013</option>
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
            <input type="submit" value="Filtrar" />
        </div>
    </form>
</div>

<div class="widget board">
<?php if (!empty($data)) : ?>
    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Datos</th>
                <th>Cantidad</th>
                <th>Proyectos</th>
                <th style="max-width: 20px;"></th>
                <th>Direcci&oacute;n</th>
                <th>CP</th>
                <th>Localidad</th>
                <th>Pa&iacute;s</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row) : ?>
            <tr>
                <td><a href="/admin/users/?id=<?php echo $row->id; ?>"><?php echo $row->email; ?></a></td>
                <td><?php echo "{$row->name} "; ?> <?php echo $row->nif; ?></td>
                <td><?php echo $row->amount; ?></td>
                <td><?php echo $row->numproj; ?></td>
                <td>
                    <?php echo ($row->pending == $row->id) ? '' : $row->pending; ?>
                    <?php if ($row->confirmed) echo ' Confirmado';
                    elseif ($row->edited) echo ' Revisado'; ?>
                    <?php if ($row->pdf) : ?>
                        <br /><a href="/document/cert/<?php echo $row->id; ?>/<?php echo $filters['year']; ?>" target="_blank">[Ver pdf]</a><br />
                        <a href="/admin/reports/resetpdf/<?php echo md5($row->pdf); ?>" onclick="return confirm('Seguro que eliminamos este pdf de certificado?');">[Eliminar pdf]</a>
                    <?php endif; ?>
                </td>
                <td><?php echo $row->address; ?></td>
                <td><?php echo $row->zipcode; ?></td>
                <td><?php echo $row->location; ?></td>
                <td><?php echo $row->country; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>
