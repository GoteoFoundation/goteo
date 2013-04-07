<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'info';

$call = $this['call'];

foreach ($call->projects as $key => $proj) {

    if ($proj->status < 3) {
        unset($call->projects[$key]);
    }
}

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

                <h2 class="title"><?php echo Text::get('call-info-main-header') ?></h2>

                <div id="call-description"><?php echo nl2br(Text::urlink($call->description)) ?></div>

                <?php if (($call->status > 3 ) && count($call->projects) > 0)
                        echo new View('view/call/widget/table.html.php', $this);
                ?>

                <h3 class="title"><?php echo Text::get('call-field-whom'); // re-usa el copy del formulario ?></h3>
                <p><?php echo nl2br(Text::urlink($call->whom)) ?></p>

                <h3 class="title"><?php echo Text::get('call-field-apply'); // re-usa el copy del formulario ?></h3>
                <p><?php echo nl2br(Text::urlink($call->apply)) ?></p>

                <h2 class="title"><?php echo Text::get('call-terms-main-header') ?></h2>

                <div id="call-description"><?php echo nl2br(Text::urlink($call->legal)) ?></div>

            </div>

            <p>
                <?php if ($call->status == 3) : //inscripcion  ?>
                    <?php if (!$call->expired) : // sigue abierta  ?>
                        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/apply" class="button red join" target="_blank"><?php echo Text::get('call-splash-apply-button') ?></a>
                    <?php endif; ?>
                <?php else : //en campaña  ?>
                    <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/projects" class="button red view"><?php echo Text::get('call-splash-see_projects-button') ?></a>
        <?php endif; ?>
            </p>
        </div>
<?php echo new View('view/call/side.html.php', $this); ?>
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