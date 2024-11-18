<?php
$project = $vars['projects'];

$account = $vars['account'];
if (!$account->vat) {
    $account->vat = 21;
}

$projectFee = round($account->fee / 100, 2);

function countTotal($invests, $method)
{
    $count = 0;

    foreach ($invests as $invest) {
        if ($invest->status < 1) continue;
        if ($invest->method !== $method) continue;

        $total = $count + 1;
    }

    return $total;
}

function calcTotal($invests, $method)
{
    $total = 0;

    foreach ($invests as $invest) {
        if ($invest->status < 1) continue;
        if ($invest->method !== $method) continue;

        $total = $total + $invest->amount;
    }

    return $total;
}

$tpvTotal = calcTotal($vars['invests'], 'tpv');
$tpvProjectFee = $tpvTotal * $projectFee;
$tpvProjectVat = $tpvProjectFee * 0.21;
$tpvGatewayFee = $tpvTotal  * 0.008;
$tpvGatewayVat = 0;

$paypalTotal = calcTotal($vars['invests'], 'paypal');
$paypalProjectFee = $paypalTotal * $projectFee;
$paypalProjectVat = $paypalProjectFee * 0.21;
$paypalGatewayFee = ($paypalTotal * 0.034) + (countTotal($vars['invests'], 'paypal') * 0.35);
$paypalGatewayVat = 0;

$poolTotal = calcTotal($vars['invests'], 'pool');
$poolProjectFee = $poolTotal * $projectFee;
$poolProjectVat = $poolProjectFee * 0.21;
$poolGatewayFee = $poolTotal * 0.02;
$poolGatewayVat = $poolGatewayFee * 0.21;

$cashTotal = calcTotal($vars['invests'], 'cash');
$cashProjectFee = $cashTotal * $projectFee;
$cashProjectVat = $cashProjectFee * 0.21;
$cashGatewayFee = $cashTotal * 0.02;
$cashGatewayVat = $cashGatewayFee * 0.21;

$totalTotal = $cashTotal + $poolTotal + $paypalTotal + $tpvTotal;
$totalProjectFee = $cashProjectFee + $poolProjectFee + $paypalProjectFee + $tpvProjectFee;
$totalProjectVat = $cashProjectVat + $poolProjectVat + $paypalProjectVat + $tpvProjectVat;
$totalGatewayFee = $cashGatewayFee + $poolGatewayFee + $paypalGatewayFee + $tpvGatewayFee;
$totalGatewayVat = $cashGatewayVat + $poolGatewayVat + $paypalGatewayVat + $tpvGatewayVat;

$reportData = [
    'TPV' => [
        'base' => \amount_format($tpvTotal, 2),
        'project_fee' => sprintf("%s (%s%%)", \amount_format($tpvProjectFee, 2), $account->fee,),
        'project_vat' => sprintf("%s (21%%)", \amount_format($tpvProjectVat, 2),),
        'gateway_fee' => sprintf("%s (0,8%%)", \amount_format($tpvGatewayFee, 2)),
        'gateway_vat' => sprintf("%s (21%%)", \amount_format($tpvGatewayVat, 2))
    ],
    'PAYPAL' => [
        'base' => \amount_format($paypalTotal, 2),
        'project_fee' => sprintf("%s (%s%%)", \amount_format($paypalProjectFee, 2), $account->fee),
        'project_vat' => sprintf("%s (21%%)", \amount_format($paypalProjectVat, 2)),
        'gateway_fee' => sprintf("%s (3,4%% + 0,35 * trxs)", \amount_format($paypalGatewayFee, 2)),
        'gateway_vat' => sprintf("%s (21%%)", \amount_format($paypalGatewayVat, 2))
    ],
    'MONEDERO' => [
        'base' => \amount_format($poolTotal, 2),
        'project_fee' => sprintf("%s (%s%%)", \amount_format($poolProjectFee, 2), $account->fee),
        'project_vat' => sprintf("%s (21%%)", \amount_format($poolProjectVat, 2)),
        'gateway_fee' => sprintf("%s (2%%)", \amount_format($poolGatewayFee, 2)),
        'gateway_vat' => sprintf("%s (21%%)", \amount_format($poolGatewayVat, 2))
    ],
    'MANUAL' => [
        'base' => \amount_format($cashTotal, 2),
        'project_fee' => sprintf("%s (%s%%)", \amount_format($cashProjectFee, 2), $account->fee),
        'project_vat' => sprintf("%s (21%%)", \amount_format($cashProjectVat, 2)),
        'gateway_fee' => sprintf("%s (2%%)", \amount_format($cashGatewayFee, 2)),
        'gateway_vat' => sprintf("%s (21%%)", \amount_format($cashGatewayVat, 2)),
    ],
    'TOTAL' => [
        'base' => \amount_format($totalTotal, 2),
        'project_fee' => sprintf("%s", \amount_format($totalProjectFee, 2)),
        'project_vat' => sprintf("%s", \amount_format($totalProjectVat, 2)),
        'gateway_fee' => sprintf("%s", \amount_format($totalGatewayFee, 2)),
        'gateway_vat' => sprintf("%s", \amount_format($totalGatewayVat, 2))
    ]
];

?>
<table>
    <tr>
        <td></td>
        <td>RECAUDACIÓN</td>
        <td>COMISIÓN DE GOTEO</td>
        <td>IVA</td>
        <td>COMISIONES COBRADAS A GOTEO</td>
        <td>IVA</td>
    </tr>
    <?php foreach ($reportData as $key => $value): ?>
        <tr>
            <td><?= $key ?></td>
            <td><?= $value['base'] ?></td>
            <td><?= $value['project_fee'] ?></td>
            <td><?= $value['project_vat'] ?></td>
            <td><?= $value['gateway_fee'] ?></td>
            <td><?= $value['gateway_vat'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>