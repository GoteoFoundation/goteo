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
            <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
            <dd class="light-violet"><span><?php echo \amount_format($call->amount) ?></span></dd>

            <dt><?php echo Text::get('call-splash-remain_budget-header') ?></dt>
            <dd class="violet"><span><?php echo \amount_format($call->rest) ?></span></dd>
        </dl>
    </div>
</div>