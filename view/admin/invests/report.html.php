<?php

use Goteo\Library\Text;

$project = $this['project'];
$Data = $this['reportData'];

\trace($Data);
echo '<hr />';
\trace($project);
die;

$desglose = array();
$goteo    = array();
$proyecto = array();
$estado   = array();
$usuario  = array();

$users = array();
foreach ($this['users'] as $user) {
    $amount = $users[$user->user]->amount + $user->amount;
    $users[$user->user] = (object) array(
        'name'   => $user->name,
        'user'   => $user->user,
        'amount' => $amount
    );
}

uasort($this['users'],
    function ($a, $b) {
        if ($a->name == $b->name) return 0;
        return ($a->name > $b->name) ? 1 : -1;
        }
    );

// recorremos los aportes
foreach ($this['invests'] as $invest) {

// para cada metodo acumulamos desglose, comision * 0.08, pago * 0.092
    $desglose[$invest->method] += $invest->amount;
    $goteo[$invest->method] += ($invest->amount * 0.08);
    $proyecto[$invest->method] += ($invest->amount * 0.92);
// para cada estado
    $estado[$invest->status]['total'] += $invest->amount;
    $estado[$invest->status][$invest->method] += $invest->amount;
// para cada usuario
    $usuario[$invest->user->id]['total'] += $invest->amount;
    $usuario[$invest->user->id][$invest->method] += $invest->amount;
// por metodo
    $usuario[$invest->method]['users'][$invest->user->id] = 1;
    $usuario[$invest->method]['invests']++;

}

?>
<div class="widget">
    <p>Informe de financiación de <strong><?php echo $project->name ?></strong> al d&iacute;a <?php echo date('d-m-Y') ?></p>
    <p>Se encuentra en estado <strong><?php echo $this['status'][$project->status] ?></strong>
        <?php if ($project->round > 0) : ?>
            , en <?php echo $project->round . 'ª ronda' ?> y le quedan <strong><?php echo $project->days ?> d&iacute;as</strong> para finalizarla
        <?php endif; ?>
        .</p>
    <p>El proyecto tiene un <strong>coste m&iacute;nimo de <?php echo number_format($project->mincost, 0, '', '.') ?> &euro;</strong>, un coste <strong>&oacute;ptimo de <?php echo number_format($project->maxcost, 0, '', '.') ?> &euro;</strong> y ahora mismo lleva <strong>conseguidos <?php echo number_format($project->amount, 0, '', '.') ?> &euro;</strong>, lo que representa un <strong><?php echo number_format(($project->amount / $project->mincost * 100), 2, ',', '') . '%' ?></strong> sobre el m&iacute;nimo.</p>

    <h3>Informe de aportes</h3>
    <p style="font-style:italic;">Cantidades en bruto (no se tiene en cuenta ejecuciones fallidas ni comisiones PayPal ni SaNostra)</p>

    <h4>Por destinatario</h4>
    <table>
        <tr>
            <th>M&eacute;todo</th>
            <th>Cantidad</th>
            <th>Goteo</th>
            <th>Proyecto</th>
        </tr>
        <tr>
            <td>PayPal</td>
            <td style="text-align:right;"><?php echo number_format($desglose['paypal'], 0, '', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($goteo['paypal'], 2, ',', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($proyecto['paypal'], 2, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Tpv</td>
            <td style="text-align:right;"><?php echo number_format($desglose['tpv'], 0, '', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($goteo['tpv'], 2, ',', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($proyecto['tpv'], 2, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>Cash</td>
            <td style="text-align:right;"><?php echo number_format($desglose['cash'], 0, '', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($goteo['cash'], 2, ',', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($proyecto['cash'], 2, ',', '.'); ?></td>
        </tr>
        <tr>
            <td>TOTAL</td>
            <td style="text-align:right;"><?php echo number_format(($desglose['paypal'] + $desglose['tpv'] + $desglose['cash']), 2, ',', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format(($goteo['paypal'] + $goteo['tpv'] + $goteo['cash']), 2, ',', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format(($proyecto['paypal'] + $proyecto['tpv'] + $proyecto['cash']), 2, ',', '.'); ?></td>
        </tr>
    </table>

    <h3>Por estado</h3>
    <table>
        <tr>
            <th>Estado</th>
            <th>Cantidad</th>
            <th>PayPal</th>
            <th>Tpv</th>
            <th>Cash</th>
        </tr>
        <?php foreach ($this['investStatus'] as $id=>$label) : if (in_array($id, array('-1', '2', '4'))) continue;?>
        <tr>
            <td><?php echo $label ?></td>
            <td style="text-align:right;"><?php echo number_format($estado[$id]['total'], 0, '', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($estado[$id]['paypal'], 0, '', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($estado[$id]['tpv'], 0, '', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($estado[$id]['cash'], 0, '', '.'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Por cofinanciadores (<?php echo count($this['users']) ?>)</h3>
    <table>
        <tr>
            <th>Usuario</th>
            <th>Cantidad</th>
            <th>PayPal</th>
            <th>Tpv</th>
            <th>Cash</th>
        </tr>
        <?php foreach ($this['users'] as $user) : ?>
        <tr>
            <td><?php echo $user->name ?></td>
            <td style="text-align:right;"><?php echo number_format($user->amount, 0); ?></td>
            <td style="text-align:right;"><?php echo number_format($usuario[$user->user]['paypal'], 0, '', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($usuario[$user->user]['tpv'], 0, '', '.'); ?></td>
            <td style="text-align:right;"><?php echo number_format($usuario[$user->user]['cash'], 0, '', '.'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<!-- información detallada apra tratar transferencias a proyectos -->
<div class="widget">
    <h3>Informe de transacciones correctas</h3>
    <p style="font-style:italic;">Descuenta las incidencias de conteo de usuarios/operaciones, comision y neto.</p>

<?php if (!empty($Data['tpv'])) : ?>
    <h4>TPV</h4>
    <?php
        $users_ok = count($usuarios['tpv']['users']);
        $invests_ok = $usuarios['tpv']['invests'];
        $incidencias = 0;
        $correcto = $desglose['tpv'] - $incidencias;
        $comision = $correcto * 0.008;
        $neto = $correcto - $comision;
        $neto_goteo = $neto * 0.08;
        $neto_proyecto = $neto - $neto_goteo;
    ?>
    <table>
        <tr>
            <th></th>
            <th>1a Ronda</th>
            <th>2a Ronda</th>
            <th>Total</th>
        </tr>
        <tr>
            <th>Usuarios (sin incidencias)</th>
            <td><?php echo $users_ok ?></td>
        </tr>
        <tr>
            <th>Operaciones (sin incidencias)</th>
            <td><?php echo $invests_ok ?></td>
        </tr>
        <tr>
            <th>Importe Incidencias</th>
            <td style="text-align:right;"><?php echo number_format($incidencias, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Importe Ejecutado Correcto</th>
            <td style="text-align:right;"><?php echo number_format($correcto, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Comisi&oacute;n 0,80&#37;</th>
            <td style="text-align:right;"><?php echo number_format($comision, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Neto Goteo</th>
            <td style="text-align:right;"><?php echo number_format($neto_goteo, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Neto Proyecto</th>
            <td style="text-align:right;"><?php echo number_format($neto_proyecto, 0, '', '.'); ?></td>
        </tr>
    </table>
<?php endif; ?>

<?php if (!empty($Data['paypal'])) : ?>
    <h4>PayPal</h4>
    <?php
        $users_ok = count($usuarios['paypal']['users']);
        $invests_ok = $usuarios['paypal']['invests'];
        $incidencias = 0;
        $correcto = $desglose['paypal'] - $incidencias;
        $correcto_goteo = ($correcto * 0.08);
        $correcto_proyecto = $correcto - $correcto_goteo;
        $fee_goteo = ($usuario['paypal']['invests'] * 0.35) + ($correcto_goteo * 0.034);
        $fee_proyecto = ($usuario['paypal']['invests'] * 0.35) + ($correcto_proyecto * 0.034);
        $neto_goteo = $correcto_goteo - $fee_goteo;
        $neto_proyecto = $correcto_proyecto - $fee_proyecto;
    ?>
    <table>
        <tr>
            <th></th>
            <th>1a Ronda</th>
            <th>2a Ronda</th>
            <th>Total</th>
        </tr>
        <tr>
            <th>Usuarios (sin incidencias)</th>
            <td><?php echo $users_ok ?></td>
        </tr>
        <tr>
            <th>Operaciones (sin incidencias)</th>
            <td><?php echo $invests_ok ?></td>
        </tr>
        <tr>
            <th>Importe Incidencias</th>
            <td style="text-align:right;"><?php echo number_format($incidencias, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Importe Ejecutado Correcto</th>
            <td style="text-align:right;"><?php echo number_format($correcto, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Fee a Goteo 0,35/operacion + 3,4&#37; de <?php echo number_format($correcto_goteo, 0, '', '.'); ?></th>
            <td style="text-align:right;"><?php echo number_format($fee_goteo, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Fee al Promotor 0,35/operacion + 3,4&#37; de <?php echo number_format($correcto_proyecto, 0, '', '.'); ?></th>
            <td style="text-align:right;"><?php echo number_format($fee_proyecto, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Neto Goteo</th>
            <td style="text-align:right;"><?php echo number_format($neto_goteo, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Neto Proyecto</th>
            <td style="text-align:right;"><?php echo number_format($neto_proyecto, 0, '', '.'); ?></td>
        </tr>
    </table>
<?php endif; ?>

<?php if (!empty($Data['cash'])) : ?>
    <h4>CASH</h4>
    <?php
        $users_ok = count($usuarios['cash']['users']);
        $invests_ok = $usuarios['cash']['invests'];
        $incidencias = 0;
        $correcto = $desglose['cash'] - $incidencias;
    ?>
    <table>
        <tr>
            <th></th>
            <th>1a Ronda</th>
            <th>2a Ronda</th>
            <th>Total</th>
        </tr>
        <tr>
            <th>Usuarios (sin incidencias)</th>
            <td><?php echo $users_ok ?></td>
        </tr>
        <tr>
            <th>Operaciones (sin incidencias)</th>
            <td><?php echo $invests_ok ?></td>
        </tr>
        <tr>
            <th>Importe Incidencias</th>
            <td style="text-align:right;"><?php echo number_format($incidencias, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Importe Ejecutado Correcto</th>
            <td style="text-align:right;"><?php echo number_format($correcto, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Comisi&oacute;n 0,80&#37;</th>
            <td style="text-align:right;"><?php echo number_format($comision, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Neto Goteo</th>
            <td style="text-align:right;"><?php echo number_format($neto_goteo, 0, '', '.'); ?></td>
        </tr>
        <tr>
            <th>Neto Proyecto</th>
            <td style="text-align:right;"><?php echo number_format($neto_proyecto, 0, '', '.'); ?></td>
        </tr>
    </table>
<?php endif; ?>

<?php if (!empty($Data['note'])) : ?>
    <p><?php echo implode('<br />- ', $Data['note']) ?></p>
<?php endif; ?>
</div>