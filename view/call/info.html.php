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

            <div class="freetext">

                <?php if (!empty($call->description)) : // si hay contenido  ?>
                <h2 class="title"><?php echo Text::get('call-info-main-header') ?></h2>
                <div id="call-description"><?php echo $call->description ?></div>
                <?php endif; ?>

                <?php if (($call->status > 3 ) && count($call->projects) > 0)
                        echo new View('view/call/widget/table.html.php', $this);
                ?>

                <?php if (!empty($call->whom)) : // si hay contenido  ?>
                <h3 class="title"><?php echo Text::get('call-field-whom'); // re-usa el copy del formulario ?></h3>
                <p><?php echo nl2br(Text::urlink($call->whom)) ?></p>
                <?php endif; ?>

                <?php if (!empty($call->apply)) : // si hay contenido  ?>
                <h3 class="title"><?php echo Text::get('call-field-apply'); // re-usa el copy del formulario ?></h3>
                <p><?php echo nl2br(Text::urlink($call->apply)) ?></p>
                <?php endif; ?>

                <?php if (!empty($call->legal)) : // si hay contenido  ?>
                <h2 class="title"><?php echo Text::get('call-terms-main-header') ?></h2>
                <div id="call-description"><?php echo nl2br(Text::urlink($call->legal)) ?></div>
                <?php endif; ?>

            </div>

            <p>
                <?php if ($call->status == 3) : //inscripcion  ?>
                    <?php if (!$call->expired) : // sigue abierta  ?>
                        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/apply" class="button red join" target="_blank"><?php echo Text::get('call-splash-apply-button') ?></a>
                    <?php endif; ?>
                <?php elseif (count($call->projects) > 0) : //en campaña con proyectos  ?>
                    <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/projects" class="button red view"><?php echo Text::get('call-splash-see_projects-button') ?></a>
        <?php endif; ?>
            </p>
        </div>
        <?php echo new View('view/call/side.html.php', $this); ?>
    </div>

    <?php echo new View('view/call/widget/social-responsive.html.php', $this) ?>

    <?php echo new View('view/call/bottom.html.php', $this); ?>
</div>

<?php
include 'view/call/footer.html.php';
include 'view/epilogue.html.php';
?>