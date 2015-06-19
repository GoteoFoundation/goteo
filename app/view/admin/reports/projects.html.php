<?php

use Goteo\Library\Text;

$data = $vars['data'];
$filters = $vars['filters'];

// lista de proyectos y su estado de financiación
if (is_array($data)) : ?>
<div class="widget board">
    <form id="filter-form" action="/admin/reports/projects" method="get">

        <div style="float:left;margin:5px;">
            <label for="status-filter">Estado de financiacion:</label><br />
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
                <th>Proyecto</th>
                <th>Inicio campaña</th>
                <th>Final 1a ronda</th>
                <th>Final 2a ronda</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $id=>$row) : ?>
            <tr>
                <td><a href="/admin/reports/projects/<?php echo $id; ?>">[Informe]</td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['init']; ?></td>
                <td><?php echo $row['fin_1a']; ?></td>
                <td><?php echo $row['fin_2a']; ?></td>
                <td><?php if (!empty($row['fin_1a'])) : ?><a href="/admin/contracts/<?php echo $id; ?>" target="_blank">[Contrato]<?php endif; ?></td>
                <td><a href="/admin/projects/?proj_name=<?php echo substr($row['name'], 0, 10); ?>" target="_blank">[Proyecto]</td>
                <td><a href="/project/<?php echo $id; ?>" target="_blank">[Ver]</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php // datos de financiación de un proyecto
elseif ($data instanceof stdClass) : ?>

<a href="/admin/reports/projects" class="button">Volver a la lista de proyectos</a>
<div class="widget board">
    <p>El proyecto <strong><?php echo $data->nombre_proyecto; ?></strong></p>
    <table>
        <tr>
            <td>Inició la campaña el:</td>
            <td><strong><?php echo $data->inicio_campaña; ?></strong></td>
        </tr>
        <tr>
            <td>Final de primera ronda el:</td>
            <td><strong><?php echo $data->final_1a_ronda; ?></strong></td>
        </tr>
        <tr>
            <td>Final de segunda ronda el:</td>
            <td><strong><?php echo $data->final_2a_ronda; ?></strong></td>
        </tr>
    </table>
    <hr />
    <table>
        <tr>
            <td>El impulsor es:</td>
            <td><strong>persona <?php echo $data->persona; ?></strong></td>
        </tr>
        <?php if (!empty($data->entidad)) : ?>
        <tr>
            <td>Entidad:</td>
            <td><?php echo $data->entidad; ?>, <?php echo $data->cif; ?></td>
        </tr>
        <?php endif; ?>
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
