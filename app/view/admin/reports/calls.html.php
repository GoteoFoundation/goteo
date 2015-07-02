<?php

use Goteo\Library\Text;

$data = $vars['data'];
$filters = $vars['filters'];

// lista de proyectos y su estado de financiación
if (is_array($data)) : ?>
<div class="widget board">
    <form id="filter-form" action="/admin/reports/calls" method="get">

        <div style="float:left;margin:5px;">
            <label for="status-filter">Estado de convocatoria:</label><br />
            <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                <option value=""<?php if (empty($filters['status'])) echo ' selected="selected"';?>>Cualquier estado</option>
                <option value="first"<?php if ($filters['status'] == 'first') echo ' selected="selected"';?>>En primera ronda</option>
                <option value="second"<?php if ($filters['status'] == 'second') echo ' selected="selected"';?>>En segunda ronda</option>
                <option value="completed"<?php if ($filters['status'] == 'completed') echo ' selected="selected"';?>>Terminada segunda ronda</option>
            </select>
        </div>
<!--
        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="Ver" />
        </div>
-->
    </form>
</div>

<div class="widget board">
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Convocatoria</th>
                <th>Inicio Busqueda</th>
                <th>Final Busqueda</th>
                <th>Inicio Reparto</th>
                <th>Final Reparto</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $id=>$row) : ?>
            <tr>
                <td><a href="/admin/reports/calls/<?php echo $id; ?>">[Informe]</td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['init_0']; ?></td>
                <td><?php echo $row['fin_0']; ?></td>
                <td><?php echo $row['init_1']; ?></td>
                <td><?php echo $row['fin_1']; ?></td>
                <td><a href="/admin/calls?name=<?php echo substr($row['name'], 0, 10); ?>" target="_blank">[Gestion]</td>
                <td><a href="/call/<?php echo $id; ?>" target="_blank">[Ver]</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php // datos de financiación de un proyecto
elseif ($data instanceof stdClass) : ?>

<a href="/admin/reports/calls" class="button">Volver a la lista de convocatorias</a>
<div class="widget board">
    <h3>Ajustar los datos del informe a lo necesario para gestionar el proceso</h3>
    <p>La convocatoria <strong><?php echo $data->name; ?></strong></p>
    <table>
        <tr>
            <td>Inició de busqueda:</td>
            <td><strong><?php echo $data->init_0; ?></strong></td>
        </tr>
        <tr>
            <td>Final de busqueda:</td>
            <td><strong><?php echo $data->fin_0; ?></strong></td>
        </tr>
        <tr>
            <td>Inició de reparto:</td>
            <td><strong><?php echo $data->init_1; ?></strong></td>
        </tr>
        <tr>
            <td>Final de reparto:</td>
            <td><strong><?php echo $data->fin_1; ?></strong></td>
        </tr>
    </table>
    <hr />
    <table>
        <tr>
            <td>El convocador:</td>
            <td><strong><?php echo $data->user->name; ?></strong></td>
        </tr>
        <tr>
            <td>Responsable:</td>
            <td><?php echo $data->responsable; ?>, <?php echo $data->nif_responsable; ?>, <?php echo $data->email_responsable; ?></td>
        </tr>
        <tr>
            <td>Direcci&oacute;n fiscal:</td>
            <td><?php echo $data->dir_fiscal; ?></td>
        </tr>
        <tr>
            <td>Direcci&oacute;n postal:</td>
            <td><?php echo $data->dir_postal; ?></td>
        </tr>
        <tr>
            <td>Tel&eacute;fono:</td>
            <td><?php echo $data->telefono; ?></td>
        </tr>
        <tr>
            <td>Email usuario:</td>
            <td><?php echo $data->email_usuario; ?></td>
        </tr>
        <tr>
            <td colspan="2"><br /><strong>Cuentas del proyecto</strong></td>
        </tr>
        <tr>
            <td>CCC:</td>
            <td><?php echo $data->ccc; ?></td>
        </tr>
        <tr>
            <td>PayPal:</td>
            <td><?php echo $data->paypal; ?></td>
        </tr>
    </table>
    <hr />
    <table>
        <tr>
            <td>Importe que aparece AHORA en el term&oacute;metro:</td>
            <td style="text-align: right;"><?php echo \euro_format($data->total, 2); ?> &euro;</td>
        </tr>
        <tr><td colspan="2"><br /></td></tr>
        <tr>
            <td>Dinero perdido por incidencias no resueltas:</td>
            <td style="text-align: right;"><?php echo \euro_format($data->issues, 2); ?> &euro;</td>
        </tr>
        <tr><td colspan="2"><br /></td></tr>
        <tr>
            <td>Dinero enviado al proyecto:</td>
            <td style="text-align: right;"><?php echo \euro_format($data->project_total, 2); ?> &euro;</td>
        </tr>
        <tr>
            <td>Por el banco:</td>
            <td style="text-align: right;"><strong><?php echo \euro_format($data->project_tpv, 2) ?> &euro;</strong></td>
        </tr>
        <tr>
            <td>Por paypal:</td>
            <td style="text-align: right;"><strong><?php echo \euro_format($data->project_paypal, 2) ?> &euro;</strong></td>
        </tr>
        <tr><td colspan="2"><br /></td></tr>
        <tr>
            <td>Comisiones cobradas a Goteo</td>
            <td style="text-align: right;"><?php echo \euro_format($data->fee_total, 2); ?> &euro;</td>
        </tr>
        <tr>
            <td>Por el banco:</td>
            <td style="text-align: right;"><strong><?php echo \euro_format($data->fee_tpv, 2) ?> &euro;</strong></td>
        </tr>
        <tr>
            <td>Por paypal:</td>
            <td style="text-align: right;"><strong><?php echo \euro_format($data->fee_paypal, 2) ?> &euro;</strong></td>
        </tr>
        <tr><td colspan="2"><br /></td></tr>
    </table>
    <table>
        <tr>
            <td>Num total de donantes con TODA la informaci&oacute;n rellenada que renunciaron a recompensa:</td>
            <td style="text-align: right; min-width: 30px;"><?php echo $data->num_resign; ?></td>
        </tr>
        <tr>
            <td>Num de donantes de más de 100 euros aportados:</td>
            <td style="text-align: right; min-width: 30px;"><?php echo $data->num_resign100; ?></td>
        </tr>
        <tr>
            <td>Num de usuarios que no marcaron ninguna recompensa pero tampoco donaci&oacute;n:</td>
            <td style="text-align: right; min-width: 30px;"><?php echo $data->num_noresign; ?></td>
        </tr>
    </table>
</div>

<?php endif; ?>
