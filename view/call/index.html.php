<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'info';

$call = $this['call'];

include 'view/call/prologue.html.php';
include 'view/call/header.html.php';
?>
<div id="main">
    <div id="title">
        <?php echo new View('view/call/widget/title.html.php', $this); ?>
    </div>
    <div id="banners-social">
        <?php echo new View('view/call/widget/banners.html.php', $this) ?>
        <div id="social-logo">
            <?php echo new View('view/call/widget/social.html.php', $this) ?>
        </div>
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

            <!-- en la pagina de stats esta vista va dentro de
                <ul id="list">
                    <li class="item" id="numbers">
            -->
            <?php echo new View('view/call/widget/stats.html.php', $this); ?>
        </div>
        <?php echo new View('view/call/widget/buzz.html.php', $this); ?>
    </div>
    
    <div id="supporters-links">
<!-- texto "tantos usuarios han aportado en esta campaña"
y cuadro de caritas (de usuarios que han aportado gastando riego, solo con imagen) -->
        <span><?php echo Text::get('call-header-supporters', 67) ?></span>
        <?php echo new View('view/call/widget/supporters.html.php', $this); ?>

<!--  carrusel de sponsors (ahora imagenes a saco, organizar en los contenedores necesarios y el js de slides) -->
        <span><?php echo Text::get('node-header-sponsorby') ?></span>
        <?php foreach ($call->sponsors as $sponsor) : ?>
            <div>
                <a href="<?php echo $sponsor->url ?>" target="_blank" title="<?php echo $sponsor->name ?>">
                    <img src="<?php if ($sponsor->image instanceof \Goteo\Model\Image) echo $sponsor->image->getLink(130); ?>" alt="<?php echo $sponsor->name ?>" />
                </a>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<?php
include 'view/call/footer.html.php';
include 'view/epilogue.html.php';
?>