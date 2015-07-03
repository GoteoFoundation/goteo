<?php
use Goteo\Core\View;

$calls = $this->calls;
$campaigns = $this->campaigns;
?>

<div class="widget calls">

    <div class="title">
        <div class="logo"><?php echo $this->text('home-calls-header'); ?></div>
        <?php if (!empty($calls)) : ?>
        <div class="call-count mod1">
            <strong><?php echo count($calls) ?></strong>
            <span>Convocatorias<br />abiertas</span>
        </div>
        <?php endif; ?>

        <?php if (!empty($campaigns)) : ?>
        <div class="call-count mod2">
            <strong><?php echo count($campaigns) ?></strong>
            <span>Campañas<br />activas</span>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($calls)) : ?>
    <div id="calls" class="callrow">
        <?php if (count($calls) > 1) : ?><a class="prev">prev</a><?php endif ?>
        <div class="slder_calls">
        <?php foreach ($calls as $call) : ?>
            <div class="slder_slide">
            <?php echo View::get('call/widget/call.html.php', array('call' => $call)); ?>
            </div>
        <?php endforeach; ?>
        </div>
        <?php if (count($calls) > 1) : ?><a class="next">next</a><?php endif ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($campaigns)) : ?>

    <div id="campaigns" class="callrow">
        <?php if (count($campaigns) > 1) : ?><a class="prev">prev</a><?php endif ?>
        <div class="slder_campaigns">
        <?php foreach ($campaigns as $call)  : ?>
            <div class="slder_slide">
            <?php echo View::get('call/widget/call.html.php', array('call' => $call)); ?>
            </div>
        <?php endforeach; ?>
        </div>
        <?php if (count($campaigns) > 1) : ?><a class="next">next</a><?php endif ?>
    </div>
    <?php endif; ?>

    <a class="all" href="/discover/calls"><?php echo $this->text('regular-see_all'); ?></a>
</div>

