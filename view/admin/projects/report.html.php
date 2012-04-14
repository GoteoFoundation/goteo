<?php

use Goteo\Library\Text;

$project = $this['project'];
$Data    = $this['reportData'];

?>
<style type="text/css">
    td {padding: 3px 10px;}
</style>
<div class="widget report">
    <h3 class="title">Informe de financiación para del proyecto <span style="color:#20B2B3;"><?php echo $project->name ?></span></h3>

    <?php
    $sumData['total'] = $project->amount;
    $sumData['fail']  = $Data['tpv']['total']['fail']   + $Data['paypal']['total']['fail']   + $Data['cash']['total']['fail'];
    $sumData['brute'] = $Data['tpv']['total']['amount'] + $Data['paypal']['total']['amount'] + $Data['cash']['total']['amount'];
    $sumData['tpv_fee_goteo'] = $Data['tpv']['total']['amount']  * 0.008;
    $sumData['pp_goteo'] = $Data['paypal']['total']['amount'] * 0.08;
    $sumData['pp_project'] = $Data['paypal']['total']['amount'] - $sumData['pp_goteo'];
    $sumData['pp_fee_goteo'] = ($Data['paypal']['total']['invests'] * 0.35) + ($sumData['pp_goteo'] * 0.034);
    $sumData['pp_fee_project'] = ($Data['paypal']['total']['invests'] * 0.35) + ($sumData['pp_project'] * 0.034);
    $sumData['pp_net_project'] = $sumData['pp_project'] - $sumData['pp_fee_project'];
    $sumData['fee_goteo'] = $sumData['tpv_fee_goteo'] + $sumData['pp_fee_goteo'];
    $sumData['net'] = $sumData['brute'] - $sumData['tpv_fee_goteo'] - $sumData['pp_fee_goteo'] - $sumData['pp_fee_project'];
    $sumData['goteo'] = $sumData['brute'] * 0.08;
    $sumData['restproject'] = $sumData['brute'] - $sumData['goteo'] - $sumData['pp_project'];
    ?>
<p>
    <table>
        <tr>
            <th style="text-align:left;">Resumen de recaudación</th>
        </tr>
        <tr>
            <td>- Mostrado en el termómetro de Goteo.org: <?php echo \amount_format($sumData['total'], 2) ?></td>
        </tr>
        <tr>
            <td>- Ingresado realmente descontando incidencias<strong>*</strong> (usuarios que no tienen fondos, cancelaciones, etc): <?php echo \amount_format($sumData['brute'], 2) ?></td>
        </tr>
        <tr>
            <td>- Comisión del 8&#37; para el mantenimiento de Goteo.org (a nombre de la Fundación Fuentes Abiertas): <?php echo \amount_format($sumData['goteo'], 2) ?></td>
        </tr>
    </table>
</p>
<p>
    <table>
        <tr>
            <th style="text-align:left;">Comisiones de bancos</th>
        </tr>
        <tr>
            <td>- Comisiones cobradas a Goteo por los bancos (asumidas por la Fundación, no se cobran al impulsor): <?php echo \amount_format($sumData['fee_goteo'], 2) ?></td>
        </tr>
        <tr>
            <td>- Comisiones cobradas al impulsor por PayPal (estimadas): <?php echo \amount_format($sumData['pp_fee_project'], 2) ?></td>
        </tr>
    </table>
</p>
<p>
    <table>
        <tr>
            <th style="text-align:left;">Transferencias de la Fundación Fuentes Abiertas al impulsor</th>
        </tr>
        <tr>
            <td>- Enviado a través de PayPal (sin descontar comisiones de PayPal al impulsor): <?php echo \amount_format($sumData['pp_project'], 2) ?> (/fecha/)</td>
        </tr>
        <tr>
            <td>- Enviado a través de cuenta bancaria: <?php echo \amount_format($sumData['restproject'], 2) ?> (/fecha/)</td>
        </tr>
    </table>
</p>
<p>
    <table>
        <tr>
            <th style="text-align:left;">Desglose informativo de lo pagado mediante PayPal</th>
        </tr>
        <tr>
            <td>- Cantidad transferida: <?php echo \amount_format($sumData['pp_project'], 2) ?></td>
        </tr>
        <tr>
            <td>- ComisiÃ³n aproximada cobrada al impulor: <?php echo \amount_format($sumData['pp_fee_project'], 2) ?></td>
        </tr>
        <tr>
            <td>- Cantidad aproximada recibida por el impulsor: <?php echo \amount_format($sumData['pp_net_project'], 2) ?></td>
        </tr>
    </table>
</p>

<?php if (!empty($Data['note'])) : ?>

    <p>
    <table>
        <tr>
            <th style="text-align:left;"><strong>*</strong> Pagos de usuarios con incidencias</th>
        </tr>
        <tr>
            <td><?php echo implode('<br />', $Data['note']) ?></td>
        </tr>
    </table>
    </p>
<?php endif; ?>
</div>

