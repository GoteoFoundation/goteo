<?php

$calls = $this->calls['calls'];
$campaigns = $this->calls['campaigns'];
?>
<div id="node-calls" class="content_widget rounded-corners">

    <h2><?= $this->text('node-home-calls-header') ?>
    <span class="line"></span>
    </h2>

    <?php if (!empty($calls)) : ?>
    <script type="text/javascript">
        $(function(){
            $('#n-calls').slides({
                container: 'slder_calls',
                generatePagination: false,
                play: 0
            });
        });
    </script>
    <div id="n-calls" class="callrow">
        <div class="arrow-left"><?php if (count($calls) > 1) : ?><a class="prev">prev</a><?php endif ?></div>
        <div class="slder_calls"<?php if (count($calls) == 1) : ?> style="overflow: hidden; position: relative; display: block;"<?php endif ?>>
        <?php foreach ($calls as $call) : ?>
            <div class="slder_slide">
            <?php echo View::get('call/widget/call.html.php', array('call' => $call)); ?>
            </div>
        <?php endforeach; ?></div>
        <div class="arrow-right"><?php if (count($calls) > 1) : ?><a class="next">next</a><?php endif ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($campaigns)) : ?>
    <script type="text/javascript">
        $(function(){
            $('#n-campaigns').slides({
                container: 'slder_campaigns',
                generatePagination: false,
                play: 0
            });
        });
    </script>
    <div id="n-campaigns" class="callrow">
        <div class="arrow-left"><?php if (count($campaigns) > 1) : ?><a class="prev">prev</a><?php endif ?></div>
        <div class="slder_campaigns"<?php if (count($campaigns) == 1) : ?> style="overflow: hidden; position: relative; display: block;"<?php endif ?>>
        <?php foreach ($campaigns as $campaign)  : ?>
            <div class="slder_slide">
            <?php echo View::get('call/widget/call.html.php', array('call' => $campaign)); ?>
            </div>
        <?php endforeach; ?></div>
        <div class="arrow-right"><?php if (count($campaigns) > 1) : ?><a class="next">next</a><?php endif ?></div>
    </div>
    <?php endif; ?>

    <br clear="both" />
    <a class="all" href="/discover/calls"><?= $this->text('regular-see_all') ?></a>
</div>
