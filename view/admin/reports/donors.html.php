<?php

use Goteo\Library\Text;

$data = $this['data'];
?>
<div class="widget board">
    <form id="filter-form" action="/admin/reports/donors" method="get">

        <div style="float:left;margin:5px;">
            <label for="year-filter">A&ntilde;o fiscal:</label><br />
            <select id ="year-filter" name="Year">
                <option value="2012">Hasta 2012</option>
                <option value="2013">2013</option>
            </select>
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="Ver donantes" />
        </div>
    </form>
</div>

<div class="widget board">
<?php if (!empty($data)) : ?>
    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Nif</th>
                <th>Cantidad</th>
                <th>Proyectos</th>
                <th></th>
                <th>Datos</th>
                <th>Certificado</th>
            </tr>
            <tr>
                <th colspan="3">Direcci&oacute;n</th>
                <th>CP</th>
                <th colspan="2">Localidad</th>
                <th colpsan="2">Pa&iacute;s</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row) : ?>
            <tr>
                <td><a href="/admin/users/?id=<?php echo $row->id; ?>"><?php echo $row->email; ?></a></td>
                <td><?php echo $row->name; ?></td>
                <td><?php echo $row->nif; ?></td>
                <td><?php echo $row->amount; ?></td>
                <td><?php echo $row->numproj; ?></td>
                <td><?php echo ($row->pending == $row->id) ? '' : $row->pending; ?></td>
                <td><?php echo ($row->edited) ? 'Confirmados' : ''; ?></td>
                <td><?php echo ($row->confirmed) ? 'Emitido' : ''; ?></td>
            </tr>
            <tr>
                <td><?php echo $row->address; ?></td>
                <td><?php echo $row->zipcode; ?></td>
                <td><?php echo $row->location; ?></td>
                <td><?php echo $row->country; ?></td>
            </tr>
            <tr>
                <td colspan="8"><hr /></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>