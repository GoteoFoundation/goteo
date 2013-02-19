<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'info';

$call = $this['call'];

include 'view/call/prologue.html.php';
include 'view/call/header.html.php';
?>
<div id="main">
    <?php echo new View('view/call/widget/title.html.php', $this); ?>
    <div id="banners-social">
        <?php echo new View('view/call/widget/banners.html.php', $this) ?>
        <?php echo new View('view/call/widget/social.html.php', $this) ?>
    </div>

    <div id="info" class="stats-container">
        <div id="content" style="width: 540px;">
            <p class="subtitle">
                <a href="/call/<?php echo $call->id ?>/info"><?php
            if ($call->status == 3) {
                // inscripción
                echo Text::get('call-splash-searching_projects', $call->user->name);
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

            <?php echo new View('view/call/widget/stats.html.php', $this); ?>
        </div>
        <?php echo new View('view/call/widget/buzz.html.php', $this); ?>
    </div>

    <?php if ($call->status > 3) : ?>
    <div id="supporters-sponsors">
        <?php echo new View('view/call/widget/supporters.html.php', $this); ?>
        <?php echo new View('view/call/widget/sponsors.html.php', $this); ?>
    </div>
    <?php endif; ?>
</div>

<?php
include 'view/call/footer.html.php';
include 'view/epilogue.html.php';
?>