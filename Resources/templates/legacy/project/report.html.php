<?php
use Goteo\Model\Contract;
use Goteo\Model\Invest;

$project = $vars['project'];
$account = $vars['account']; // cuentas del proyecto, para tener el porcentaje de comisión


if(!$account->vat) 
{
    $account->vat=21;
}

$matchers=$project->getMatchers('active');


$sumData['match_goteo']=0;
$matchfunding_invest=0;

if ($matchers)
{
    foreach($matchers as $matcher){
        $matchfunding_invest+=Invest::getList(['projects' => $project->id, 'users' => $matcher->owner, 'types' => 'drop', 'status' => Invest::$ACTIVE_STATUSES
                        ], null, 0, 0, 'money');

        $matcher_fee=round($matcher->fee / 100, 2);

        $sumData['match_goteo']= $matchfunding_invest * $matcher_fee;
        //Aplicamos el IVA al 50% de la comision de Goteo
        $sumData['match_goteo']=(( $sumData['match_goteo']/2)*($account->vat/100))+ $sumData['match_goteo'];

    }

}

$called = $project->called;

// comisión goteo para este proyecto
$GOTEO_FEE = round($account->fee / 100, 2);

// comisión para el riego que ha recibido el proyecto
$CALL_FEE = round($called->fee_projects_drop / 100, 2);

$Data    = $vars['Data'];
$admin = (isset($vars['admin']) && $vars['admin'] === true) ? true : false;

//restamos a los aportes recibios de monedero lo correspondiente a matchers
$Data['pool']['total']['amount']=$Data['pool']['total']['amount']-$matchfunding_invest;

$total_issues = 0;
foreach ($Data['issues'] as $issue) {
    $total_issues += $issue->amount;
}

// si tiene registro de contrato
list($cNum, $cDate) = Contract::getNum($project->id, $project->published);
$cName = "P-{$cNum}-{$cDate}";
?>
<style type="text/css">
    td {padding: 3px 10px;}
</style>
<div class="widget report">
    <h3 class="title" style="text-transform: none;">Informe de financiación del proyecto <?php echo $cName; ?><br /><span style="color:#20B2B3;"><?php echo $project->name ?></span></h3>

    <?php
    // tanto los aportes de riego como los cash-no-cobrados: aparecen en el termómetro, cobran comisión, pero no se incluyen en el previsto a transferir
    $sumData['total'] = $Data['tpv']['total']['amount'] + $Data['paypal']['total']['amount'] + $Data['cash']['total']['amount'] + $Data['pool']['total']['amount'];
    $sumData['drop'] = $Data['drop']['total']['amount'];
    $sumData['ghost'] = $Data['ghost']['total']['amount'];
    $sumData['fail']  = $total_issues;
    // $sumData['shown'] = $sumData['total'] + $sumData['fail'] + $sumData['drop'] + $sumData['pool'] + $sumData['ghost'];
    $sumData['shown'] = Invest::getList(['projects' => $project->id, 'status' =>
                        [Invest::STATUS_PENDING,
                        Invest::STATUS_CHARGED,
                        Invest::STATUS_CANCELLED,
                        Invest::STATUS_TO_POOL,
                        Invest::STATUS_PAID
                        ]], null, 0, 0, 'money');
    $sumData['cancelled'] = Invest::getList(['projects' => $project->id, 'status' => [Invest::STATUS_CANCELLED, Invest::STATUS_TO_POOL]], null, 0, 0, 'money');
    $sumData['tpv_fee_goteo'] = $Data['tpv']['total']['amount']  * 0.008;

    $sumData['cash_goteo'] = $Data['cash']['total']['amount']  * $GOTEO_FEE;
    //Aplicamos el IVA al 50% de la comision de Goteo
    $sumData['cash_goteo']=(($sumData['cash_goteo']/2)*($account->vat/100))+$sumData['cash_goteo'];

    $sumData['tpv_goteo'] = $Data['tpv']['total']['amount']  * $GOTEO_FEE;
    //Aplicamos el IVA al 50% de la comision de Goteo
    $sumData['tpv_goteo']=(($sumData['tpv_goteo']/2)*($account->vat/100))+$sumData['tpv_goteo'];

    $sumData['pp_goteo'] = $Data['paypal']['total']['amount'] * $GOTEO_FEE;
    //Aplicamos el IVA al 50% de la comision de Goteo
    $sumData['pp_goteo']=(($sumData['pp_goteo']/2)*($account->vat/100))+$sumData['pp_goteo'];

    $sumData['drop_goteo'] = $Data['drop']['total']['amount'] * $CALL_FEE;
    //Aplicamos el IVA al 50% de la comision de Goteo
    $sumData['drop_goteo']=(($sumData['drop_goteo']/2)*($account->vat/100))+$sumData['drop_goteo'];

    $sumData['pool_goteo'] = $Data['pool']['total']['amount'] * $GOTEO_FEE;
    //Aplicamos el IVA al 50% de la comision de Goteo
    $sumData['pool_goteo']=(($sumData['pool_goteo']/2)*($account->vat/100))+$sumData['pool_goteo'];

    $sumData['ghost_goteo'] = $Data['ghost']['total']['amount'] * $GOTEO_FEE;
    //Aplicamos el IVA al 50% de la comision de Goteo
    $sumData['ghost_goteo']=(($sumData['ghost_goteo']/2)*($account->vat/100))+$sumData['ghost_goteo'];

    $sumData['pp_project'] = $Data['paypal']['total']['amount'] - $sumData['pp_goteo'];
    $sumData['pp_fee_goteo'] = ($Data['paypal']['total']['invests'] * 0.35) + ($Data['paypal']['total']['amount'] * 0.034);
    $sumData['pp_fee_project'] = ($Data['paypal']['total']['invests'] * 0.35) + ($sumData['pp_project'] * 0.034);
    $sumData['pp_net_project'] = $sumData['pp_project'] - $sumData['pp_fee_project'];
    $sumData['fee_goteo'] = $sumData['tpv_fee_goteo'] + $sumData['pp_fee_goteo'];
    $sumData['goteo'] = $sumData['cash_goteo'] + $sumData['tpv_goteo'] + $sumData['pp_goteo'] + $sumData['drop_goteo'] + $sumData['pool_goteo'] + $sumData['ghost_goteo'] +$sumData['match_goteo']; // si que se descuenta la comisión sobre capital riego

    $sumData['total_fee_project'] = $sumData['fee_goteo'] + $sumData['goteo']; // este es el importe de la factura

    $sumData['tpv_project'] = $sumData['total'] - $sumData['fee_goteo'] - $sumData['goteo'] - $sumData['pp_project'];
    $sumData['project'] = $sumData['total'] - $sumData['fee_goteo'] - $sumData['goteo'];
    // * el capital riego no lo manda goteo, lo manda el convocador
    ?>
<p>
    <?php
    $unique = ($project->one_round) ? ' (y única)' : '';
    if (!empty($project->passed)) {
        echo 'El proyecto terminó la primera'.$unique.' ronda el día <strong>'.date('d/m/Y', strtotime($project->passed)).'</strong>.<br />';
    } else {
        echo 'El proyecto terminará la primera'.$unique.' ronda el día <strong>'.date('d/m/Y', strtotime($project->willpass)).'</strong>.<br />';
    } ?>

    <?php if (!$project->one_round && !empty($project->success)) {
        echo 'El proyecto terminó la segunda ronda el día <strong>'.date('d/m/Y', strtotime($project->success)).'</strong>.';
    } elseif (empty($project->success)) {
        echo 'El proyecto terminará la segunda ronda el día <strong>'.date('d/m/Y', strtotime($project->willfinish)).'</strong>.<br />';
    } ?>
<br />
<br />
    Env&iacute;o [fecha transferencia] correo electr&oacute;nico <?php echo $project->user->email; ?>
</p>
<br />

    <table>
        <tr>
            <th style="text-align:left;">1) Resumen de recaudación</th>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Máximo mostrado en el termómetro de Goteo.org al cerrar la campaña (<?php echo ($project->success) ? date('d/m/Y', strtotime($project->success)) : 'fecha'; ?>): <strong><?php echo \amount_format($sumData['shown'], 2); ?></strong>
            <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(Puede que no se haya llegado nunca a esta cifra si han devuelto aportes antes del cierre de campaña)</em></td>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Aportes cancelados manualmente desde el admin o devueltos al monedero del usuario: <strong style="color:red">-<?php echo \amount_format($sumData['cancelled'], 2); ?></strong></td>
        </tr>
        <?php if (!empty($Data['issues'])) : ?>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Incidencias (Usuarios/as que no tienen fondos en su cuenta, tarjetas desactualizadas, cancelaciones, reembolsos...) : <strong><?php echo \amount_format($sumData['fail'], 2); ?></strong> (<strong>*</strong> ver listado más abajo)</td>
        </tr>
        <?php endif ?>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Total recaudado: <strong><?php echo \amount_format($sumData['total'], 2); ?></strong> (importe de las ayudas monetarias recibidas)</td>
        </tr>
        <?php if (!empty($called)) : ?>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Total Capital Riego: <strong><?php echo \amount_format($sumData['drop']); ?></strong> (Transferencia del convocador '<?php echo $project->called->user->name ?>' directamente al impulsor)</td>
        </tr>
        <?php endif; ?>

         <?php if ($matchers) : ?>
            <?php foreach($matchers as $matcher): ?>
                <tr>
                    <td>-&nbsp;&nbsp;&nbsp;&nbsp;Riego obtenido del <strong>Canal <?= $matcher->name ?></strong>: <strong><?= amount_format($matchfunding_invest)  ?></strong> (A transferir de forma directa)</td>
                </tr>
                <tr>
                    <td>
                        <b>Comisión matcher:</b> <?= $matcher->fee.'%' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($sumData['ghost'])) : ?>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Otro recibido: <strong><?php echo \amount_format($sumData['ghost']); ?></strong> (Aporte manual sin ingreso bancario)</td>
        </tr>
        <?php endif; ?>
    </table>
<br />

    <table>
        <tr>
            <th style="text-align:left;">2) Gastos derivados de la financiación del proyecto</th>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Comisiones cobradas a Goteo por cada transferencia de tarjeta (0,8&#37;) y PayPal (3,4&#37; + 0,35 por transacción/usuario/a): <strong>total <?php echo \amount_format($sumData['fee_goteo'], 2); ?></strong></td>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Comisión del <?php echo $account->fee; ?>&#37; de Goteo.org <?= $account->vat ? '(Incluye un '.$account->vat.'&#37; de IVA para el 50&#37; de la cantidad)' : '' ?>:  <strong><?php echo \amount_format($sumData['goteo'], 2); ?></strong>
                <?php if($project->called): ?>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(<?= \amount_format($sumData['drop_goteo']) .' de comisión en la parte correspondiente a capital riego al aplicar un '.$called->fee_projects_drop .'%)' ?></em>
                <?php endif; ?>
                <?php if($matchers): ?>
                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(<?= \amount_format($sumData['match_goteo']) .' de comisión en la parte correspondiente a riego' ?>)</em>
                <?php endif; ?>
            </td>
            <td>

            </td>
        </tr>
<?php if ($admin) : ?>
        <!--
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Desglose calculo Comisión del 8&#37; de Goteo.org: <?php $aportes = $Data['tpv']['total']['amount'] + $Data['paypal']['total']['amount'] + $Data['cash']['total']['amount']; $comisionaportes = $aportes * 0.08; $comisionriego = $Data['drop']['total']['amount'] * 0.08; echo "Aportes usuarios: {$Data['tpv']['total']['amount']}(tpv) + {$Data['paypal']['total']['amount']}(paypal) + {$Data['cash']['total']['amount']}(cash) = {$aportes} -> {$comisionaportes} Capital Riego: {$Data['drop']['total']['amount']} -> {$comisionriego}"; ?></td>
        </tr>
        -->
<?php endif; ?>
        <tr>
            <td>Por el total de estas comisiones  la Fundación Goteo ha emitido la factura <strong>[N&uacute;mero de factura]</strong> por importe de <strong><?php echo \amount_format($sumData['total_fee_project'], 2); ?></strong>, a nombre de la persona o entidad que firma el contrato</td>
        </tr>
    </table>
<br />

    <table>
        <tr>
            <th style="text-align:left;">3) Transferencias de la Fundación Goteo (Goteo.org) a los/as  impulsores/as</th>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Envío a través de PayPal (sin descontar comisiones de PayPal  de 3,4&#37;+ 0,35  por transacción/usuario/a, cobradas automáticamente al receptor del dinero): <strong><?php echo \amount_format($sumData['pp_project'], 2); ?> ([fecha transferencia])</strong></td>
        </tr>
        <tr>
            <td>-&nbsp;&nbsp;&nbsp;&nbsp;Envío a través de cuenta bancaria: <strong><?php echo \amount_format($sumData['tpv_project'], 2); ?> ([fecha transferencia])</strong></td>
        </tr>
        <tr>
            <td>En estas cantidades ya se ha descontado el importe de la factura [N&uacute;mero de factura] por importe de <?php echo \amount_format($sumData['total_fee_project'], 2); ?></td>
        </tr>
    </table>
<br />

    <table>
        <tr>
            <th style="text-align:left;"><strong>Total dinero enviado a las cuentas bancarias del proyecto: <?php echo \amount_format($sumData['project'], 2); ?></strong> (<?php echo \amount_format($sumData['total'], 2); ?> - <?php echo \amount_format($sumData['total_fee_project'], 2); ?>)</th>
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
        <?php foreach ($Data['issues'] as $issue) :
            $warst = ($issue->status == 1) ? ' style="color: red !important;"' : '';
            ?>
        <tr>
<?php if ($admin) : ?>
            <td><?php echo '<a href="/admin/accounts/details/'.$issue->invest.'" target="_blank"'.$warst.'>[Ir al aporte]</a> Usuario <a href="/admin/users/manage/' . $issue->user . '" target="_blank">' . $issue->userName . '</a> [<a href="mailto:'.$issue->userEmail.'">'.$issue->userEmail.'</a>], ' . $issue->statusName . ', ' . $issue->amount . ' euros.'; if (!empty($warst)) echo '  (Aporte: '.$issue->invest.')'; ?></td>
<?php else: ?>
            <td<?php echo $warst; ?>>Usuario/a <?php echo $issue->userName; ?>,  <?php echo $issue->statusName; ?>, <?php echo $issue->amount . ' ss.'; if (!empty($warst)) echo '  (Aporte: '.$issue->invest.')';?></td>
<?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </table>

    <br />
    <table>
        <tr>
            <td><strong>TOTAL</strong> (no cobrado)<strong>: <?php echo \amount_format($sumData['fail']); ?></strong></td>
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
