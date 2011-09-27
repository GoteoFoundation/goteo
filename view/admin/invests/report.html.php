<?php

use Goteo\Library\Text;

$project = $this['project'];
?>
<div class="widget">
    <h3>Informe de financiación</h3>
    <p><strong>Proyecto:</strong> <?php echo $project->name ?> (<?php echo $this['status'][$project->status] ?>)</p>
    <dl>
        <dt>Total conseguido (estados 0 o 1)</dt>
        <dd>
            Tanto % sobre mínimo<br/>
            De lo cual es tanto paypal tanto tpv y tanto cash
        </dd>
    </dl>
    <dl>
        <dt>Total ejecutado (estado 1)</dt>
        <dd>
            Tanto.<br />
            De lo cual es tanto paypal tanto tpv y tanto cash
        </dd>
    </dl>
    <dl>
        <dt>Total pendiente (estado 0)</dt>
        <dd>
            Tanto.<br />
            De lo cual es tanto paypal tanto tpv y tanto cash
        </dd>
    </dl>
</div>

<div class="widget">
    <h3>Cofinanciadores</h3>
    <p><?php foreach ($this['users'] as $investor) { echo "{$investor->name} {$investor->amount} &euro;<br />";} ?>
    </p>
</div>

<!--
<div class="widget">
    <h3>Aportes</h3>
    <p><?php # foreach ($this['invests'] as $invest) { echo "{$invest->date} {$invest->amount} &euro; <br />";} ?>
</div>
-->

    <p><?php # echo '<pre>' . print_r($this['project'], 1) . '</pre>'; ?></p>

