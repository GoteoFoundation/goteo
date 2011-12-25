<?php

use Goteo\Library\Text;

$call = $this['call'];

?>
<div class="widget project-called collapsable activable" id="project-called">
    <div class="explain">
        <dl>
            <dt>Este proyecto recibe aportes de la campaña:</dt>
            <dd><?php echo $call->name ?></dd>
        </dl>
        <p>Por cada <strong>1&euro;</strong> que aportes a este proyecto, <?php echo $call->user->name ?> aporta otro <strong>1€</strong>.</p>
    </div>
    <div class="amount">
        <dl>
            <dt>Presupuesto total de campaña</dt>
            <dd class="light-violet"><strong><?php echo $call->amount ?> <span class="euro">&euro;</span></strong></dd>

            <dt>Queda por repartir</dt>
            <dd class="violet"><strong><?php echo $call->rest ?> <span class="euro">&euro;</span></strong></dd>
        </dl>
    </div>
</div>