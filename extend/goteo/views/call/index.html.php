<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'info';

$call = $vars['call'];

include __DIR__ . '/prologue.html.php';
include __DIR__ . '/header.html.php';

?>
<div id="main">
    <?php echo View::get('call/widget/title.html.php', $vars); ?>
    <div id="banners-social">
        <?php
        echo View::get('call/widget/banners.html.php', $vars);
        echo View::get('call/widget/social.html.php', $vars);
        ?>
    </div>

    <div id="info" class="stats-container">
        <div id="content">
            <p class="subtitle">
                <a href="/call/<?php echo $call->id ?>/info"><?php
            if ($call->status == 3) {
                // inscripción
                echo empty($call->amount) ?
                    Text::recorta($call->resources, 200)
                    :
                    Text::get('call-splash-searching_projects', $call->user->name)
                ;
            } elseif (!empty($call->amount)) {
                //en campaña con dinero
                echo Text::html('call-splash-invest_explain', $call->user->name);
                if (!empty($call->maxdrop)) {
                    echo Text::html('call-splash-drop_limit', $call->maxdrop);
                }
            } else {
                //en campaña sin dinero, con recursos
                echo Text::recorta($call->resources, 200);
            } ?></a>
            </p>

            <p class="subtitle" style="color: #58595b; font-size: 12px;"><?php echo $call->subtitle ?></p>

            <?php echo View::get('call/widget/stats.html.php', $vars); ?>

        </div>
        <?php echo View::get('call/widget/buzz.html.php', $vars); ?>
    </div>

    <?php echo View::get('call/widget/social-responsive.html.php', $vars) ?>

    <?php echo View::get('call/bottom.html.php', $vars); ?>
</div>

<?php
include __DIR__ . '/footer.html.php';
include __DIR__ . '/../../../../app/view/epilogue.html.php';
?>
