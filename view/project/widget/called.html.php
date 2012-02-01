<?php

use Goteo\Library\Text;

$call = $this['call'];

?>
<div class="widget project-called collapsable activable" id="project-called">
    <div class="explain">
        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info" class="expand" target="_blank"></a>
        <dl>
            <dt>Este proyecto recibe aportes de la campaña:</dt>
            <dd><?php echo $call->name ?></dd>
        </dl>
        <p><?php echo Text::html('call-splash-invest_explain_this', $call->user->name) ?></p>
    </div>
    <div class="amount">
        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info" class="expand" target="_blank"></a>
        <dl>
            <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
            <dd class="light-violet"><span><?php echo \amount_format($call->amount) ?></span></dd>
        </dl>

        <dl>
            <dt><?php echo Text::get('call-splash-remain_budget-header') ?></dt>
            <dd class="violet"><span><?php echo \amount_format($call->rest) ?></span></dd>
        </dl>
    </div>
</div>
<script type="text/javascript">

    $(function () {
       if ($('div.widget.project-called div.explain').height() > $('div.widget.project-called div.amount').height()) {
           $('div.widget.project-called div.amount').height($('div.widget.project-called div.explain').height());
       } else {
           $('div.widget.project-called div.explain').height($('div.widget.project-called div.amount').height());
       }
    });

</script>
