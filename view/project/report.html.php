<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$project = $this['project'];
$Data    = $this['Data'];
$admin = (isset($this['admin']) && $this['admin'] === true) ? true : false;

$total_issues = 0;
foreach ($Data['issues'] as $issue) {
    $total_issues += $issue->amount;
}
?>
<style type="text/css">
    td {padding: 3px 10px;}
</style>
<div class="widget report">
    <h3 class="title">Informe de financiación del proyecto<br /><span style="color:#20B2B3;"><?php echo $project->name ?></span></h3>

    <?php
    $sumData['total'] = $Data['tpv']['total']['amount'] + $Data['paypal']['total']['amount'] + $Data['cash']['total']['amount'];
    $sumData['fail']  = $total_issues;
    $sumData['shown'] = $sumData['total'] + $sumData['fail'];
    $sumData['tpv_fee_goteo'] = $Data['tpv']['total']['amount']  * 0.008;
    $sumData['cash_goteo'] = $Data['cash']['total']['amount']  * 0.08;
    $sumData['tpv_goteo'] = $Data['tpv']['total']['amount']  * 0.08;
    $sumData['pp_goteo'] = $Data['paypal']['total']['amount'] * 0.08;
    $sumData['pp_project'] = $Data['paypal']['total']['amount'] - $sumData['pp_goteo'];
    $sumData['pp_fee_goteo'] = ($Data['paypal']['total']['invests'] * 0.35) + ($Data['paypal']['total']['amount'] * 0.034);
    $sumData['pp_fee_project'] = ($Data['paypal']['total']['invests'] * 0.35) + ($sumData['pp_project'] * 0.034);
    $sumData['pp_net_project'] = $sumData['pp_project'] - $sumData['pp_fee_project'];
    $sumData['fee_goteo'] = $sumData['tpv_fee_goteo'] + $sumData['pp_fee_goteo'];
    $sumData['goteo'] = $sumData['cash_goteo'] + $sumData['tpv_goteo'] + $sumData['pp_goteo'];
    $sumData['tpv_project'] = $sumData['total'] - $sumData['fee_goteo'] - $sumData['goteo'] - $sumData['pp_project'];
    $sumData['project'] = $sumData['total'] - $sumData['fee_goteo'] - $sumData['goteo'];
    $sumData['drop'] = $Data['drop']['total']['amount'];
    ?>
<p>
    <?php if (!empty($project->passed)) {
        echo 'El proyecto terminó la primera ronda el día <strong>'.date('d/m/Y', strtotime($project->passed)).'</strong>.<br />';
    } else {
        echo 'El proyecto terminará la primera ronda el día <strong>'.date('d/m/Y', strtotime($project->willpass)).'</strong>.<br />';
    } ?>

    <?php if (!empty($project->success)) {
        echo 'El proyecto terminó la segunda ronda el día <strong>'.date('d/m/Y', strtotime($project->success)).'</strong>.';
    } ?>

</p>
<br />

    <table>
        <tr>
            <th style="text-align:left;">1) Resumen de recaudación</th>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Mostrado en el termómetro de Goteo.org al cerrar la campaña (<?php echo (empty($project->success)) ? 'fecha' : date('d/m/Y', strtotime($project->success)); ?>): <strong><?php echo \amount_format($sumData['shown'], 2).' &euro;'; ?></strong></td>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Incidencias (Usuarios/as que no tienen fondos en su cuenta, tarjetas desactualizadas, cancelaciones, reembolsos...) : <strong><?php echo \amount_format($sumData['fail'], 2).' &euro;'; ?></strong> (<strong>*</strong> ver listado más abajo)</td>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Ingresado realmente descontando incidencias: <strong><?php echo \amount_format($sumData['total'], 2).' &euro;'; ?></strong></td>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Comisión del 8&#37; de Goteo.org: <strong><?php echo \amount_format($sumData['goteo'], 2).' &euro;'; ?></strong> (factura que se emitirá por la Fundación Fuentes Abiertas a nombre de la persona o entidad que firma el contrato)</td>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Capital riego de la campa&ntilde; <?php echo $project->called->name ?>: <strong><?php echo \amount_format($sumData['drop']).' &euro;'; ?></strong> (Transferencia de <?php echo $project->called->user->name ?> directamente al impulsor)</td>
        </tr>
    </table>
<br />

    <table>
        <tr>
            <th style="text-align:left;">2) Comisiones de bancos</th>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Comisiones cobradas a Goteo por cada transferencia de tarjeta (0,8&#37;) y PayPal (3,4&#37; + 0,35 por transacción/usuario/a): <strong>total <?php echo \amount_format($sumData['fee_goteo'], 2).' &euro;'; ?></strong></td>
        </tr>
    </table>
<br />

    <table>
        <tr>
            <th style="text-align:left;">3) Transferencias de la Fundación Fuentes Abiertas (Goteo.org) a los/as  impulsores/as</th>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Envío previsto a través de PayPal (sin descontar comisiones de PayPal  de 3,4&#37;+ 0,35  por transacción/usuario/a, cobradas automáticamente al receptor del dinero): <strong><?php echo \amount_format($sumData['pp_project'], 2).' &euro;' ?></strong></td>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Envío previsto a través de cuenta bancaria: <strong><?php echo \amount_format($sumData['tpv_project'], 2).' &euro;' ?></strong></td>
        </tr>
        <tr>
            <td>En estas cantidades ya está descontada la partida del  8&#37; (<?php echo \amount_format($sumData['goteo'], 2).' euros'; ?>) y las comisiones de los bancos cobradas a Goteo.org (<?php echo \amount_format($sumData['fee_goteo'], 2).' euros'; ?>)</td>
        </tr>
    </table>
<br />

    <table>
        <tr>
            <th style="text-align:left;">Total dinero por enviar a las cuentas bancarias del proyecto: <?php echo \amount_format($sumData['project'], 2).' &euro;' ?></th>
        </tr>
        <tr>
            <td>(ojo:  de la parte recaudada y enviada a través de PayPal aún faltará descontar la comisión bancaria correspondiente a PayPal que se carga cuando se envía el dinero a los/las beneficiarios/as).</td>
        </tr>
    </table>

<?php if ($admin) : ?>
    <table>
        <tr>
            <th style="text-align:left;">Desglose informativo de lo pagado mediante PayPal</th>
        </tr>
        <tr>
            <td>- Cantidad transferida: <?php echo \amount_format($sumData['pp_project'], 2) ?></td>
        </tr>
        <tr>
            <td>- Comisión aproximada cobrada al impulor: <?php echo \amount_format($sumData['pp_fee_project'], 2) ?></td>
        </tr>
        <tr>
            <td>- Cantidad aproximada recibida por el impulsor: <?php echo \amount_format($sumData['pp_net_project'], 2) ?></td>
        </tr>
    </table>
<br />
<?php endif; ?>

<?php if (!empty($Data['issues'])) : ?>
    <br />
    <table>
        <tr>
            <th style="text-align:left;">* Listado de usuarios/as con incidencias en su cuenta PayPal.</th>
        </tr>
        <tr>
            <td>Estos son los aportes con problemas en payPal que no se han conseguido cobrar y se han cancelado.</td>
        </tr>
    </table>

    <br />
    <table>
        <?php foreach ($Data['issues'] as $issue) : ?>
        <tr>
<?php if ($admin) : ?>
            <td><?php echo '<a href="/admin/accounts/details/'.$issue->invest.'" target="_blank">[Ir al aporte]</a> Usuario <a href="/admin/users/manage/' . $issue->user . '" target="_blank">' . $issue->userName . '</a> [<a href="mailto:'.$issue->userEmail.'">'.$issue->userEmail.'</a>], ' . $issue->statusName . ', ' . $issue->amount . ' euros.'; ?></td>
<?php else: ?>
            <td>Usuario/a <?php echo $issue->userName; ?>,  <?php echo $issue->statusName; ?>, <?php echo $issue->amount . ' euros'; ?></td>
<?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </table>

    <br />
    <table>
        <tr>
            <td><strong>TOTAL</strong> (no cobrado)<strong>: <?php echo $sumData['fail']; ?> &euro;</strong></td>
        </tr>
    </table>
<?php endif; ?>

<?php if ($admin && !empty($Data['notes'])) : ?>
    <table>
        <tr>
            <th style="text-align:left;">Notas para el admin al generar los datos del informe. </th>
        </tr>
        <tr>
            <td>La mayoría harán referencia a las incidencias (o a aportes que no están en el estado que deberían en este punto de la campaña)</td>
        </tr>
        <?php foreach ($Data['notes'] as $note) : ?>
        <tr>
            <td><?php echo $note; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<br />
<?php endif; ?>


</div>