<?php

$invest = $this->invest;
$project = $this->project;
$calls = $this->calls;
$droped = $this->droped;
$user = $this->user;

$rewards = $invest->rewards;
array_walk($rewards, function (&$reward) { $reward = $reward->reward; });

?>
<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>
<div class="widget">
    <p>
        <strong>Proyecto:</strong> <?php echo $project->name ?> (<?php echo $this->status[$project->status] ?>)
        <strong>Usuario: </strong><?php echo $user->name ?>
    </p>

    <h3>Detalles del aporte</h3>
    <?php if (!empty($invest->call)) : ?>
    <dl>
        <dt>Riego de la Convocatoria:</dt>
        <dd><?php echo $calls[$invest->call] ?></dd>
    </dl>
    <?php endif; ?>
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
        <dd><?php echo $this->status[$invest->status]; if ($invest->status < 0) echo ' <span style="font-weight:bold; color:red;">OJO! que este aporte no fue confirmado.<span>';  ?></dd>
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
                if (!empty($invest->campaign))
                    echo '<br />Capital riego';

                if (!empty($invest->anonymous))
                    echo '<br />Aporte anónimo';

                if (!empty($invest->resign))
                    echo "<br />Donativo de: {$invest->address->name} [{$invest->address->nif}]";

                if (!empty($invest->admin))
                    echo '<br />Manual generado por admin: '.$invest->admin;
            ?>
        </dd>
    </dl>

    <?php if (!empty($invest->rewards)) : ?>
    <dl>
        <dt>Recompensas elegidas:</dt>
        <dd>
            <?php echo implode(', ', $rewards); ?>
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

    <?php if (!empty($droped)) : ?>
    <h3>Capital riego asociado</h3>
    <dl>
        <dt>Convocatoria:</dt>
        <dd><?php echo $calls[$droped->call] ?></dd>
    </dl>
    <a href="/admin/invests/details/<?php echo $droped->id ?>" target="_blank">Ver aporte completo de riego</a>
    <?php endif; ?>

</div>

<?php $this->replace() ?>
