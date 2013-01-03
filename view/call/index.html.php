<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'info';

$call = $this['call'];

// quitar proyectos financiados
foreach ($call->projects as $key => $proj) {
    if ($proj->status < 3 || $proj->status > 5) {
        unset($call->projects[$key]);
    }
}

include 'view/call/prologue.html.php';
include 'view/call/header.html.php';
?>
<div id="main">

    <?php echo new View('view/call/side.html.php', $this); ?>

    <div id="content">
        <?php echo new View('view/call/widget/title.html.php', $this); ?>

        <div class="freetext">

            <h2 class="title"><?php echo Text::get('call-info-main-header') ?></h2>

            <div id="call-description"><?php echo nl2br(Text::urlink($call->description)) ?></div>

            <h3 class="title"><?php echo Text::get('call-info-whom-header') ?></h3>
            <p><?php echo nl2br(Text::urlink($call->whom)) ?></p>

            <?php if ($call->status == 3) : //inscripcion ?>
                <h3 class="title"><?php echo Text::get('call-info-apply-header') ?></h3>
                <p><?php echo nl2br(Text::urlink($call->apply)) ?></p>
            <?php elseif (count($call->projects) > 0) : //en campaña ?>

                <h3><?php echo Text::get('call-splash-selected_projects-header') ?></h3>

            <?php endif; ?>

        </div>

        <p class="block">
            <a class="aqua" href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/terms"><?php echo Text::get('call-terms-main-header') ?></a>
        </p>
        <p>
<?php if ($call->status == 3) : //inscripcion  ?>
    <?php if (!$call->expired) : // sigue abierta  ?>
                    <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/apply" class="button red join" target="_blank"><?php echo Text::get('call-splash-apply-button') ?></a>
    <?php endif; ?>
<?php else : //en campaña ?>
                <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/projects" class="button red view"><?php echo Text::get('call-splash-see_projects-button') ?></a>
<?php endif; ?>
        </p>
    </div>

</div>

<?php
include 'view/call/footer.html.php';
include 'view/epilogue.html.php';
?>