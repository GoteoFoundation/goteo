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
    <h3>Operaciones</h3>
    <p>
        <?php if ($invest->status == 1) : ?>
            <a href="/admin/invests/return/<?php echo $invest->id ?>"
                onclick="return confirm('¿Estás seguro de querer echar atrás toda la transacción?');"
                class="button red">Devolver el dinero</a>
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
        <dd><?php echo $this['investStatus'][$invest->status] ?></dd>
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
        <dd><?php echo $invest->method; ?></dd>
    </dl>

    <dl>
        <dt>Códigos de seguimiento: <a href="/admin/invests/details/<?php echo $invest->id ?>">Ir al aporte</a></dt>
        <dd><?php
                if (!empty($invest->preapproval))
                    echo 'Preapproval: '.$invest->preapproval . '   ';

                if (!empty($invest->payment))
                    echo 'Payment: '.$invest->payment . '   ';

                if (!empty($invest->transaction))
                    echo 'Transaction: '.$invest->transaction . '   ';
            ?>
        </dd>
    </dl>

    <dl>
        <dt>Detalles del preapproval: </dt>
        <dd>Peticion paypal/tpv</dd>
    </dl>

    <dl>
        <dt>Detalles del cargo: </dt>
        <dd>Peticion paypal/tpv</dd>
    </dl>

    <dl>
        <dt>Detalles de la transaccion: </dt>
        <dd>Peticion paypal/tpv</dd>
    </dl>

</div>
