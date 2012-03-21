<?php

use Goteo\Library\Text;

$project = $this['project'];
$Data    = $this['reportData'];

if (!$project instanceof Model\Project) {
    throw new Redirection('/admin/projects');
}

?>
<style type="text/css">
    td {padding: 3px 10px;}
</style>
<div class="widget report">
    <p>Informe de financiación de <strong><?php echo $project->name ?></strong> al d&iacute;a <?php echo date('d-m-Y') ?></p>
    <p>Se encuentra en estado <strong><?php echo $this['status'][$project->status] ?></strong>
        <?php if ($project->round > 0) : ?>
            , en <?php echo $project->round . 'ª ronda' ?> y le quedan <strong><?php echo $project->days ?> d&iacute;as</strong> para finalizarla
        <?php endif; ?>
        .</p>
    <p>El proyecto tiene un <strong>coste m&iacute;nimo de <?php echo \amount_format($project->mincost) ?> &euro;</strong>, un coste <strong>&oacute;ptimo de <?php echo \amount_format($project->maxcost) ?> &euro;</strong> y ahora mismo lleva <strong>conseguidos <?php echo \amount_format($project->amount) ?> &euro;</strong>, lo que representa un <strong><?php echo \amount_format(($project->amount / $project->mincost * 100), 2, ',', '') . '%' ?></strong> sobre el m&iacute;nimo.</p>

    <h4>Resumen de financiación</h4>
    <table>
        <?php
        $sumData['total'] = $project->amount;
        $sumData['fail']  = $Data['tpv']['total']['fail']   + $Data['paypal']['total']['fail']   + $Data['cash']['total']['fail'];
        $sumData['brute'] = $Data['tpv']['total']['amount'] + $Data['paypal']['total']['amount'] + $Data['cash']['total']['amount'];
        $sumData['tpv_fee_goteo'] = $Data['tpv']['total']['amount']  * 0.008;
        $sumData['pp_goteo'] = $Data['paypal']['total']['amount'] * 0.08;
        $sumData['pp_project'] = $Data['paypal']['total']['amount'] - $sumData['pp_goteo'];
        $sumData['pp_fee_goteo'] = ($Data['paypal']['total']['invests'] * 0.35) + ($sumData['pp_goteo'] * 0.034);
        $sumData['pp_fee_project'] = ($Data['paypal']['total']['invests'] * 0.35) + ($sumData['pp_project'] * 0.034);
        $sumData['restfee'] = $sumData['tpv_fee_goteo'] + $sumData['pp_fee_goteo'];
        $sumData['net'] = $sumData['brute'] - $sumData['tpv_fee_goteo'] - $sumData['pp_fee_goteo'] - $sumData['pp_fee_project'];
        $sumData['goteo'] = $sumData['net'] * 0.08;
        $sumData['ppproject'] = $sumData['pp_project'] - $sumData['pp_fee_project'];
        $sumData['restproject'] = $sumData['brute'] - $sumData['goteo'] - $sumData['tpv_fee_goteo'] - $sumData['pp_fee_goteo'] - $sumData['ppproject'];
        ?>
        <tr>
            <th style="text-align:left;">Recaudación comprometida (visualizada en el termometro del proyecto)</th>
            <td style="text-align:right;"><?php echo \amount_format($sumData['total'], 2) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">No cobrados por falta de fondos o cancelaciones</th>
            <td style="text-align:right;"><?php echo \amount_format($sumData['fail'], 2) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">Ingresado realmente</th>
            <td style="text-align:right;"><?php echo \amount_format($sumData['brute'], 2) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">Comisión Paypal ya cobrada al impulsor <strong>(!)</strong></th>
            <td style="text-align:right;"><?php echo \amount_format($sumData['pp_fee_project'], 2) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">Comisiones cobradas a Goteo (Paypal y targetas bancarias) <strong>(!)</strong></th>
            <td style="text-align:right;"><?php echo \amount_format($sumData['restfee'], 2) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">Neto de dinero ingresado  (ingresado menos comisiones bancos) <strong>(!)</strong></th>
            <td style="text-align:right;"><?php echo \amount_format($sumData['net'], 2) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">8&#37; comisión de Goteo (del neto) <strong>(!)</strong></th>
            <td style="text-align:right;"><?php echo \amount_format($sumData['goteo'], 2) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">Pagado a proyecto mediante paypal <strong>(!)</strong></th>
            <td style="text-align:right;"><?php echo \amount_format($sumData['ppproject'], 2) ?></td>
        </tr>
        <tr>
            <th style="text-align:left;">Pendiente de pagar al proyecto <strong>(!)</strong></th>
            <td style="text-align:right;"><?php echo \amount_format($sumData['restproject'], 2) ?></td>
        </tr>
    </table>
</div>

