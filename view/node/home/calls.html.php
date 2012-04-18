<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$calls = $this['calls'];
$campaigns = $this['campaigns'];
?>
<div id="node-calls" class="content_widget">

    <h2><?php echo Text::get('home-promotes-header'); ?></h2>
    <!--- ---------------- -->

    <?php if (!empty($calls)) : ?>
    <script type="text/javascript">
        $(function(){
            $('#calls').slides({
                container: 'slder_calls',
                generatePagination: false,
                play: 0
            });
        });
    </script>
    <div id="calls" class="callrow">
        <?php if (count($calls) > 1) : ?><a class="prev">prev</a><?php endif ?>
        <div class="slder_calls">
        <?php foreach ($calls as $call) : ?>
            <div class="slder_slide">
            <?php echo new View('view/call/widget/call.html.php', array('call' => $call)); ?>
            </div>
        <?php endforeach; ?>
        </div>
        <?php if (count($calls) > 1) : ?><a class="next">next</a><?php endif ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($campaigns)) : ?>
    <script type="text/javascript">
        $(function(){
            $('#campaigns').slides({
                container: 'slder_campaigns',
                generatePagination: false,
                play: 0
            });
        });
    </script>
    <div id="campaigns" class="callrow">
        <?php if (count($campaigns) > 1) : ?><a class="prev">prev</a><?php endif ?>
        <div class="slder_campaigns">
        <?php foreach ($campaigns as $call)  : ?>
            <div class="slder_slide">
            <?php echo new View('view/call/widget/call.html.php', array('call' => $call)); ?>
            </div>
        <?php endforeach; ?>
        </div>
        <?php if (count($campaigns) > 1) : ?><a class="next">next</a><?php endif ?>
    </div>
    <?php endif; ?>

    <a class="all" href="/discover/calls"><?php echo Text::get('regular-see_all'); ?></a>
</div>