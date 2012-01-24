<?php

use Goteo\Library\Text;

$project = $this['project'];

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

    <h3>Desglose del obtenido</h3>
    <p style="font-style:italic;">Cantidades en bruto (no se tiene en cuenta comisión PayPal ni SaNostra)</p>

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