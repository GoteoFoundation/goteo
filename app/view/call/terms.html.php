<?php

use Goteo\Library\Page,
    Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'terms';

$call = $vars['call'];

include __DIR__ . '/../call/prologue.html.php';
include __DIR__ . '/../call/header.html.php';
?>

<div id="main">
    <?php echo View::get('call/widget/title.html.php', $vars); ?>
    <div id="banners-social">
        <?php echo View::get('call/widget/banners.html.php', $vars) ?>
        <?php echo View::get('call/widget/social.html.php', $vars) ?>
    </div>

    <div id="info">
        <div id="content">

            <div class="freetext">

                <h2 class="title"><?php echo Text::get('call-terms-main-header') ?></h2>

                <div id="call-terms"><?php echo nl2br(Text::urlink($call->legal)) ?></div>

            </div>

            <p class="block">
                <a class="aqua" href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info"><?php echo Text::get('call-info-main-header') ?></a>
            </p>
            <p>
                <?php if ($call->status == 3) : //inscripcion ?>
                    <?php if (!$call->expired) : // sigue abierta ?>
                        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/apply" class="button red join" target="_blank"><?php echo Text::get('call-splash-apply-button') ?></a>
                    <?php endif; ?>
                <?php else : //en campaña ?>
                    <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/projects" class="button red view"><?php echo Text::get('call-splash-see_projects-button') ?></a>
                <?php endif; ?>
            </p>
        </div>
        <?php echo View::get('call/side.html.php', $vars); ?>
    </div>

    <?php echo View::get('call/widget/social-responsive.html.php', $vars) ?>

    <?php echo View::get('call/bottom.html.php', $vars); ?>
</div>

<?php
include __DIR__ . '/../call/footer.html.php';
include __DIR__ . '/../epilogue.html.php';
?>
