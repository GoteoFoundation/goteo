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
        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>"><img src="<?php echo $call->logo->getLink(250, 124, true) ?>" alt="<?php echo $call->user->name ?>" class="logo" /></a>
        <?php echo new View('view/call/widget/title.html.php', $this); ?>
    </div>
    <div id="banners-social">
        <?php echo new View('view/call/widget/banners.html.php', $this) ?>
        <div id="social-logo">
            <ul>
                <?php if (!empty($user->facebook)): ?>
                <li class="facebook"><a href="<?php echo htmlspecialchars($user->facebook) ?>"><?php echo Text::get('regular-facebook'); ?></a></li>
                <?php endif ?>
                <?php if (!empty($user->google)): ?>
                <li class="google"><a href="<?php echo htmlspecialchars($user->google) ?>"><?php echo Text::get('regular-google'); ?></a></li>
                <?php endif ?>
                <?php if (!empty($user->twitter)): ?>
                <li class="twitter"><a href="<?php echo htmlspecialchars($user->twitter) ?>"><?php echo Text::get('regular-twitter'); ?></a></li>
                <?php endif ?>
                <?php if (!empty($user->identica)): ?>
                <li class="identica"><a href="<?php echo htmlspecialchars($user->identica) ?>"><?php echo Text::get('regular-identica'); ?></a></li>
                <?php endif ?>
                <?php if (!empty($user->linkedin)): ?>
                <li class="linkedin"><a href="<?php echo htmlspecialchars($user->linkedin) ?>"><?php echo Text::get('regular-linkedin'); ?></a></li>
                <?php endif ?>
            </ul>
            <a href="<?php echo SITE_URL ?>/service/resources" id="capital" target="_blank"><?php echo Text::get('footer-service-resources') ?></a>

            <!-- texto "difunde esta iniciativa"
            y espacio donde irán los botones
            -->

        </div>
    </div>

    <div id="info">
        <div id="content">
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
y cuadro de caritas --->
    

<!--  carrusel de sponsors (ahora imagenes a saco, organizar en los contenedores necesarios y el js de slides) -->
        <span><?php echo Text::get('node-header-sponsorby') ?></span>
        <?php foreach ($call->sponsors as $sponsor) : ?>
            <div>
                <a href="<?php echo $sponsor->url ?>" target="_blank" title="<?php echo $sponsor->name ?>"><img src="<?php if ($sponsor->image instanceof \Goteo\Model\Image)
        echo $sponsor->image->getLink(130); ?>" alt="<?php echo $sponsor->name ?>" /></a>
            </div>
        <?php endforeach; ?>

    </div>
</div>

<?php
include 'view/call/footer.html.php';
include 'view/epilogue.html.php';
?>