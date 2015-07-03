<?php
use Goteo\Core\View;

$calls = $this->calls;

?>

<div class="widget calls">

    <div class="title">
        <div class="logo"><?php echo $this->text('home-calls-header'); ?></div>
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

</div>
