<?php

use Goteo\Core\View;

$project = $this->project;
$account = $this->account; // cuentas del proyecto, para tener el porcentaje de comisión

$GOTEO_FEE = round($account->fee / 100, 2);

$Data = $this->Data;

$desglose = array();
$goteo    = array();
$proyecto = array();
$estado   = array();
$usuario  = array();

// recorremos los aportes
foreach ($this->invests as $invest) {

// para cada metodo acumulamos desglose, comision, pago
    $desglose[$invest->method] += $invest->amount;
    $goteo[$invest->method] += ( in_array($invest->method, ['drop']) ? 0 : $invest->amount * $GOTEO_FEE );
    $proyecto[$invest->method] += ( $invest->amount * (1 - $GOTEO_FEE) );
// para cada estado
    $estado[$invest->status]['total'] += $invest->amount;
    $estado[$invest->status][$invest->method] += $invest->amount;
// para cada usuario
    $usuario[$invest->user]['user'] = $invest->getUser() ? $invest->getUser() : $invest->user;
    $usuario[$invest->user]['total'] += $invest->amount;
    $usuario[$invest->user][$invest->method] += $invest->amount;
// // por metodo
    $metodos[$invest->method]['users'][$invest->user] = 1;
    $metodos[$invest->method]['invests']++;

}

?>
<?php $this->layout('admin/layout') ?>

<?php $this->section('head') ?>
<style type="text/css">
    td {padding: 3px 10px;}
</style>
<?php $this->append() ?>


<?php $this->section('admin-content') ?>
<div class="widget report">
    <p>Informe de financiación de <strong><?php echo $project->name ?></strong> al d&iacute;a <?php echo date('d-m-Y') ?></p>
    <p>Se encuentra en estado <strong><?php echo $this->projectStatus[$project->status] ?></strong>
        <?php if ($project->round > 0) : ?>
            , en <?php echo $project->round . 'ª ronda' ?> y le quedan <strong><?php echo $project->days ?> d&iacute;as</strong> para finalizarla
        <?php endif; ?>
        .</p>
    <p>El proyecto tiene un <strong>coste m&iacute;nimo de <?php echo \euro_format($project->mincost) ?> &euro;</strong>, un coste <strong>&oacute;ptimo de <?php echo \euro_format($project->maxcost) ?> &euro;</strong> y ahora mismo lleva <strong>conseguidos <?php echo \euro_format($project->amount) ?> &euro;</strong>, lo que representa un <strong><?php echo \euro_format(($project->amount / $project->mincost * 100), 2, ',', '') . '%' ?></strong> sobre el m&iacute;nimo.</p>

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
        <?php
            $tot1 = $tot2 = $tot3 = 0;
            foreach($this->methods as $method => $name):
                $tot1 += $desglose[$method];
                $tot2 += $goteo[$method];
                $tot3 += $proyecto[$method];
        ?>
        <tr>
            <td><?= $name ?></td>
            <td style="text-align:right;"><?php echo \euro_format($desglose[$method]) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($goteo[$method], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($proyecto[$method], 2) ?></td>
        </tr>
        <?php endforeach ?>

        <tr>
            <td>TOTAL</td>
            <td style="text-align:right;"><?php echo \euro_format($tot1, 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($tot2, 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($tot3, 2) ?></td>
        </tr>
    </table>

    <h3>Por estado</h3>
    <table>
        <tr>
            <th>Estado</th>
            <th>Cantidad</th>
            <?php foreach($this->methods as $method => $name): ?>
                <th><?= $name ?></th>
            <?php endforeach ?>
        </tr>
        <?php foreach ($this->status as $id=>$label) : if (in_array($id, array('-1'))) continue;?>
        <tr>
            <td><?php echo $label ?></td>
            <td style="text-align:right;"><?php echo \euro_format($estado[$id]['total']) ?></td>
            <?php foreach($this->methods as $method => $name): ?>
                <td style="text-align:right;"><?php echo \euro_format($estado[$id][$method]) ?></td>
            <?php endforeach ?>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Por cofinanciadores (<?php echo count($this->users) ?>)</h3>
    <table>
        <tr>
            <th>Usuario</th>
            <th>Cantidad</th>
            <?php foreach($this->methods as $method => $name): ?>
                <th><?= $name ?></th>
            <?php endforeach ?>
        </tr>
        <?php foreach ($usuario as $user => $parts) : ?>
        <tr>
            <td><?php echo $parts['user']->name ?></td>
            <td style="text-align:right;"><?php echo \euro_format($parts['total'], 0) ?></td>
            <?php foreach($this->methods as $method => $name): ?>
                <td style="text-align:right;"><?php echo \euro_format($parts[$method]) ?></td>
            <?php endforeach ?>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- resumen financiero proyecto -->
    <a name="detail">&nbsp;</a>
    <?php echo View::get('project/report.html.php', array('project'=>$project, 'account'=>$account, 'Data'=>$Data, 'admin'=>true)); ?>
    <hr>

<div class="widget">
<!-- información detallada apra tratar transferencias a proyectos -->
    <h3 class="title">Desglose de financiación por rondas</h3>
    <b>NOTA: ESTO NO ESTÁ MUY BIEN, HAY QUE REHACERLO</b>
    <p style="font-style:italic;">Las incidencias NO se tienen en cuenta en el conteo de usuarios/operaciones ni en importes ni en comisiones ni en netos.</p>

<?php if (!empty($Data['tpv'])) : ?>
    <h4>TPV</h4>
    <table>
        <tr>
            <th></th>
            <th>1a Ronda</th>
            <th>2a Ronda</th>
            <th>Total</th>
            <th></th>
        </tr>
        <tr>
            <th>Nº Usuarios</th>
            <td style="text-align:right;"><?php echo count($Data['tpv']['first']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['tpv']['second']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['tpv']['total']['users']) ?></td>
            <td></td>
        </tr>
        <tr>
            <th>Nº Operaciones</th>
            <td style="text-align:right;"><?php echo $Data['tpv']['first']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['tpv']['second']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['tpv']['total']['invests'] ?></td>
            <td></td>
        </tr>
        <tr>
            <th>Importe</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['first']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['second']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['total']['amount']) ?></td>
            <td></td>
        </tr>
        <tr>
            <?php
            $Data['tpv']['first']['fee']  = $Data['tpv']['first']['amount']  * 0.008;
            $Data['tpv']['second']['fee'] = $Data['tpv']['second']['amount'] * 0.008;
            $Data['tpv']['total']['fee']  = $Data['tpv']['total']['amount']  * 0.008;
            ?>
            <th>Comisi&oacute;n</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['first']['fee'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['second']['fee'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['total']['fee'], 2) ?></td>
            <td>banco 0,80&#37; de cada operaci&oacute;n</td>
        </tr>
        <tr>
            <?php
            $Data['tpv']['first']['net']  = $Data['tpv']['first']['amount']  - $Data['tpv']['first']['fee'];
            $Data['tpv']['second']['net'] = $Data['tpv']['second']['amount'] - $Data['tpv']['second']['fee'];
            $Data['tpv']['total']['net']  = $Data['tpv']['total']['amount']  - $Data['tpv']['total']['fee'];
            ?>
            <th>Neto</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['first']['net'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['second']['net'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['total']['net'], 2) ?></td>
            <td></td>
        </tr>
        <tr>
            <?php
            $Data['tpv']['first']['goteo']  = $Data['tpv']['first']['net']  * $GOTEO_FEE;
            $Data['tpv']['second']['goteo'] = $Data['tpv']['second']['net'] * $GOTEO_FEE;
            $Data['tpv']['total']['goteo']  = $Data['tpv']['total']['net']  * $GOTEO_FEE;
            ?>
            <th>Goteo</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['first']['goteo'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['second']['goteo'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['total']['goteo'], 2) ?></td>
            <td>8&#37; del neto</td>
        </tr>
        <tr>
            <?php
            $Data['tpv']['first']['project']  = $Data['tpv']['first']['net']  - $Data['tpv']['first']['goteo'];
            $Data['tpv']['second']['project'] = $Data['tpv']['second']['net'] - $Data['tpv']['second']['goteo'];
            $Data['tpv']['total']['project']  = $Data['tpv']['total']['net']  - $Data['tpv']['total']['goteo'];
            ?>
            <th>Proyecto</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['first']['project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['second']['project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['tpv']['total']['project'], 2) ?></td>
            <td>92&#37; del neto</td>
        </tr>
    </table>
<?php endif; ?>

<?php if (!empty($Data['paypal'])) : ?>
    <h4>PayPal</h4>
    <table>
        <tr>
            <th></th>
            <th>1a Ronda</th>
            <th>2a Ronda</th>
            <th>Total</th>
            <th></th>
        </tr>
        <tr>
            <th>Nº Usuarios</th>
            <td style="text-align:right;"><?php echo count($Data['paypal']['first']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['paypal']['second']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['paypal']['total']['users']) ?></td>
            <td>Sin incidencias</td>
        </tr>
        <tr>
            <th>Nº Operaciones</th>
            <td style="text-align:right;"><?php echo $Data['paypal']['first']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['paypal']['second']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['paypal']['total']['invests'] ?></td>
            <td>Sin incidencias</td>
        </tr>
        <tr>
            <th>Importe Incidencias</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['first']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['second']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['total']['fail']) ?></td>
            <td></td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['ok']  = $Data['paypal']['first']['amount'];
            $Data['paypal']['second']['ok'] = $Data['paypal']['second']['amount'];
            $Data['paypal']['total']['ok']  = $Data['paypal']['total']['amount'];
            ?>
            <th>Importe</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['first']['ok']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['second']['ok']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['total']['ok']) ?></td>
            <td>Preapprovals ejecutados correctamente</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['goteo']  = $Data['paypal']['first']['ok'] * $GOTEO_FEE;
            $Data['paypal']['second']['goteo'] = $Data['paypal']['second']['ok'] * $GOTEO_FEE;
            $Data['paypal']['total']['goteo']  = $Data['paypal']['total']['ok'] * $GOTEO_FEE;
            ?>
            <th>Goteo</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['first']['goteo'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['second']['goteo'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['total']['goteo'], 2) ?></td>
            <td>8&#37; de las operaciones correctas</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['project']  = $Data['paypal']['first']['ok']  - $Data['paypal']['first']['goteo'];
            $Data['paypal']['second']['project'] = $Data['paypal']['second']['ok'] - $Data['paypal']['second']['goteo'];
            $Data['paypal']['total']['project']  = $Data['paypal']['total']['ok']  - $Data['paypal']['total']['goteo'];
            ?>
            <th>Proyecto</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['first']['project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['second']['project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['total']['project'], 2) ?></td>
            <td>92&#37; de las operaciones correctas</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['fee_total']  = ($Data['paypal']['first']['invests'] * 0.35) + ($Data['paypal']['first']['ok'] * 0.034);
            $Data['paypal']['second']['fee_total'] = ($Data['paypal']['second']['invests'] * 0.35) + ($Data['paypal']['second']['ok'] * 0.034);
            $Data['paypal']['total']['fee_total']  = ($Data['paypal']['total']['invests'] * 0.35) + ($Data['paypal']['total']['ok'] * 0.034);
            ?>
            <th>Comisión Total</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['first']['fee_total'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['second']['fee_total'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['total']['fee_total'], 2) ?></td>
            <td>0,35 por operacion + 3,4&#37; del importe total (100&#37; del correcto)</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['fee_goteo']  = ($Data['paypal']['first']['invests'] * 0.35) + ($Data['paypal']['first']['goteo'] * 0.034);
            $Data['paypal']['second']['fee_goteo'] = ($Data['paypal']['second']['invests'] * 0.35) + ($Data['paypal']['second']['goteo'] * 0.034);
            $Data['paypal']['total']['fee_goteo']  = ($Data['paypal']['total']['invests'] * 0.35) + ($Data['paypal']['total']['goteo'] * 0.034);
            ?>
            <th>Comisión parte Goteo</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['first']['fee_goteo'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['second']['fee_goteo'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['total']['fee_goteo'], 2) ?></td>
            <td>0,35 por operacion + 3,4&#37; del importe de goteo (<?php echo \GOTEO_FEE; ?>&#37; del correcto)</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['fee_project']  = ($Data['paypal']['first']['invests'] * 0.35) + ($Data['paypal']['first']['project'] * 0.034);
            $Data['paypal']['second']['fee_project'] = ($Data['paypal']['second']['invests'] * 0.35) + ($Data['paypal']['second']['project'] * 0.034);
            $Data['paypal']['total']['fee_project']  = ($Data['paypal']['total']['invests'] * 0.35) + ($Data['paypal']['total']['project'] * 0.034);
            ?>
            <th>Comisión al Promotor</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['first']['fee_project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['second']['fee_project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['total']['fee_project'], 2) ?></td>
            <td>0,35 por operacion + 3,4&#37; del importe del proyecto (<?php echo 100 - \GOTEO_FEE; ?>&#37; del correcto)</td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['net_goteo']  = $Data['paypal']['first']['goteo']  - $Data['paypal']['first']['fee_goteo'];
            $Data['paypal']['second']['net_goteo'] = $Data['paypal']['second']['goteo'] - $Data['paypal']['second']['fee_goteo'];
            $Data['paypal']['total']['net_goteo']  = $Data['paypal']['total']['goteo']  - $Data['paypal']['total']['fee_goteo'];
            ?>
            <th>Neto Goteo</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['first']['net_goteo'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['second']['net_goteo'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['total']['net_goteo'], 2) ?></td>
            <td></td>
        </tr>
        <tr>
            <?php
            $Data['paypal']['first']['net_project']  = $Data['paypal']['first']['project']  - $Data['paypal']['first']['fee_project'];
            $Data['paypal']['second']['net_project'] = $Data['paypal']['second']['project'] - $Data['paypal']['second']['fee_project'];
            $Data['paypal']['total']['net_project']  = $Data['paypal']['total']['project']  - $Data['paypal']['total']['fee_project'];
            ?>
            <th>Neto Proyecto</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['first']['net_project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['second']['net_project'], 2) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['paypal']['total']['net_project'], 2) ?></td>
            <td></td>
        </tr>
    </table>
<?php endif; ?>

<?php if (!empty($Data['cash'])) : ?>
    <h4>CASH</h4>
    <?php
        $users_ok = count($metodos['cash']['users']);
        $invests_ok = $metodos['cash']['invests'];
        $incidencias = 0;
        $correcto = $desglose['cash'] - $incidencias;
    ?>
    <table>
        <tr>
            <th></th>
            <th>1a Ronda</th>
            <th>2a Ronda</th>
            <th>Total</th>
            <th></th>
        </tr>
        <tr>
            <th>Nº Usuarios</th>
            <td style="text-align:right;"><?php echo count($Data['cash']['first']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['cash']['second']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['cash']['total']['users']) ?></td>
            <td></td>
        </tr>
        <tr>
            <th>Nº Operaciones</th>
            <td style="text-align:right;"><?php echo $Data['cash']['first']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['cash']['second']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['cash']['total']['invests'] ?></td>
            <td></td>
        </tr>
        <tr>
            <th>Incidencias</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['first']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['second']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['total']['fail']) ?></td>
            <td>Aportes manuales con incidencia (?)</td>
        </tr>
        <tr>
            <th>Importe</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['first']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['second']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['total']['amount']) ?></td>
            <td>Aportes de cash</td>
        </tr>
        <tr>
            <?php
            $Data['cash']['first']['goteo']  = $Data['cash']['first']['amount'] * $GOTEO_FEE;
            $Data['cash']['second']['goteo'] = $Data['cash']['second']['amount'] * $GOTEO_FEE;
            $Data['cash']['total']['goteo']  = $Data['cash']['total']['amount'] * $GOTEO_FEE;
            ?>
            <th>Goteo</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['first']['goteo']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['second']['goteo']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['total']['goteo']) ?></td>
            <td>8&#37; del importe</td>
        </tr>
        <tr>
            <?php
            $Data['cash']['first']['project']  = $Data['cash']['first']['amount']  - $Data['cash']['first']['goteo'];
            $Data['cash']['second']['project'] = $Data['cash']['second']['amount'] - $Data['cash']['second']['goteo'];
            $Data['cash']['total']['project']  = $Data['cash']['total']['amount']  - $Data['cash']['total']['goteo'];
            ?>
            <th>Proyecto</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['first']['project']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['second']['project']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['cash']['total']['project']) ?></td>
            <td>92&#37; del importe</td>
        </tr>
    </table>
<?php endif; ?>

<?php if (!empty($Data['drop'])) : ?>
    <h4>RIEGO</h4>
    <?php
        $users_ok = count($metodos['drop']['users']);
        $invests_ok = $metodos['drop']['invests'];
        $incidencias = 0;
        $correcto = $desglose['drop'] - $incidencias;
    ?>
    <table>
        <tr>
            <th></th>
            <th>1a Ronda</th>
            <th>2a Ronda</th>
            <th>Total</th>
            <th></th>
        </tr>
        <tr>
            <th>Nº Usuarios</th>
            <td style="text-align:right;"><?php echo count($Data['drop']['first']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['drop']['second']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['drop']['total']['users']) ?></td>
            <td></td>
        </tr>
        <tr>
            <th>Nº Operaciones</th>
            <td style="text-align:right;"><?php echo $Data['drop']['first']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['drop']['second']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['drop']['total']['invests'] ?></td>
            <td></td>
        </tr>
        <tr>
            <th>Incidencias</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['first']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['second']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['total']['fail']) ?></td>
            <td>Aportes de Capital Riego activos en campaña cerrada (o algo así)</td>
        </tr>
        <tr>
            <th>Importe</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['first']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['second']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['total']['amount']) ?></td>
            <td>Capital riego conseguido</td>
        </tr>
        <tr>
            <?php
            $Data['drop']['first']['goteo']  = 0;
            $Data['drop']['second']['goteo'] = 0;
            $Data['drop']['total']['goteo']  = 0;
            ?>
            <th>Goteo</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['first']['goteo']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['second']['goteo']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['total']['goteo']) ?></td>
            <td>El riego no tiene comisi&oacute;n Goteo</td>
        </tr>
        <tr>
            <?php
            $Data['drop']['first']['project']  = $Data['drop']['first']['amount'];
            $Data['drop']['second']['project'] = $Data['drop']['second']['amount'];
            $Data['drop']['total']['project']  = $Data['drop']['total']['amount'];
            ?>
            <th>Proyecto</th>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['first']['project']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['second']['project']) ?></td>
            <td style="text-align:right;"><?php echo \euro_format($Data['drop']['total']['project']) ?></td>
            <td>Todo el riego</td>
        </tr>
    </table>
<?php endif; ?>

</div>

<?php $this->replace() ?>
