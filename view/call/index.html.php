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

    <div id="info">
        <div id="content">
            <?php if ($call->status == 3) : //inscripcion ?>
                <p class="subtitle red"><?php echo Text::get('call-splash-searching_projects') ?></p>
            <?php elseif (!empty($call->amount)) : //en campaña con dinero ?>
                <!--p class="subtitle"><?php echo Text::html('call-splash-invest_explain', $call->user->name) ?></p-->
            <?php else : //en campaña sin dinero, con recursos ?>
                <p class="subtitle"><?php echo Text::recorta($call->resources, 200) ?></p>
            <?php endif; ?>

            <?php echo new View('view/call/widget/stats.html.php', $this); ?>
        </div>
        <?php echo new View('view/call/widget/buzz.html.php', $this); ?>
    </div>

    <div id="supporters-sponsors">
        <?php echo new View('view/call/widget/supporters.html.php', $this); ?>
        <?php echo new View('view/call/widget/sponsors.html.php', $this); ?>
    </div>
</div>

<?php
include 'view/call/footer.html.php';
include 'view/epilogue.html.php';
?>