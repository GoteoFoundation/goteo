<?php

use Goteo\Library\Text;

$invest = $this['invest'];
$project = $this['project'];
$campaign = $this['campaign'];
$user = $this['user'];

?>
<div class="widget">
    <p>
        <strong>Proyecto:</strong> <?php echo $project->name ?> (<?php echo $this['status'][$project->status] ?>)
        <strong>Usuario: </strong><?php echo $user->name ?> [<?php echo $user->email ?>]
    </p>
    <p>
        <?php if ($project->status == 3 && ($invest->status < 1 || ($invest->method == 'tpv' && $invest->status < 2))) : ?>
        <a href="/admin/invests/cancel/<?php echo $invest->id ?>"
            onclick="return confirm('¿Estás seguro de querer cancelar este aporte y su preapproval?');"
            class="button red">Cancelar este aporte</a>&nbsp;&nbsp;&nbsp;
        <?php endif; ?>

        <?php if ($project->status == 3 && $invest->method == 'paypal' && $invest->status == 0) : ?>
        <a href="/admin/invests/execute/<?php echo $invest->id ?>"
            onclick="return confirm('¿Seguro que quieres ejecutar ahora? ¿No quieres esperar a la ejecución automática al final de la ronda? ?');"
            class="button red">Ejecutar cargo ahora</a>
        <?php endif; ?>
    </p>

    <h3>Detalles del aporte</h3>
    <dl>
        <dt>Cantidad aportada:</dt>
        <dd><?php echo $invest->amount ?> &euro;
            <?php
                if (!empty($invest->campaign))
                    echo 'Campaña: ' . $campaign->name;
            ?>
        </dd>
    </dl>
    
    <dl>
        <dt>Estado:</dt>
        <dd><?php echo $this['investStatus'][$invest->status]; if ($invest->status < 0) echo ' <span style="font-weight:bold; color:red;">OJO! que este aporte no fue confirmado.<span>';  ?></dd>
    </dl>

    <dl>
        <dt>Fecha del aporte:</dt>
        <dd><?php echo $invest->invested . '  '; ?>
            <?php
                if (!empty($invest->charged))
                    echo 'Cargo ejecutado el: ' . $invest->charged;

                if (!empty($invest->returned))
                    echo 'Dinero devuelto el: ' . $invest->returned;
            ?>
        </dd>
    </dl>

    <dl>
        <dt>Método de pago:</dt>
        <dd><?php echo $invest->method . '   '; ?>
            <?php
                if (!empty($invest->anonymous))
                    echo '<br />Aporte anónimo';

                if (!empty($invest->resign))
                    echo "<br />Donativo de: {$invest->address->name} [{$invest->address->nif}]";

                if (!empty($invest->admin))
                    echo '<br />Manual generado por admin: '.$invest->admin;
            ?>
        </dd>
    </dl>

    <dl>
        <dt>Códigos de seguimiento: <a href="/admin/accounts/details/<?php echo $invest->id ?>">Ir a la transacción</a></dt>
        <dd><?php
                if (!empty($invest->preapproval))
                    echo 'Preapproval: '.$invest->preapproval . '   ';
                
                if (!empty($invest->payment)) 
                    echo 'Cargo: '.$invest->payment . '   ';
            ?>
        </dd>
    </dl>

    <?php if (!empty($invest->rewards)) : ?>
    <dl>
        <dt>Recompensas elegidas:</dt>
        <dd>
            <?php echo implode(', ', $investData->rewards); ?>
        </dd>
    </dl>
    <?php endif; ?>

    <dl>
        <dt>Dirección:</dt>
        <dd>
            <?php echo $invest->address->address; ?>,
            <?php echo $invest->address->location; ?>,
            <?php echo $invest->address->zipcode; ?>
            <?php echo $invest->address->country; ?>
        </dd>
    </dl>
</div>
