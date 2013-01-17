<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'splash';

$call = $this['call'];

include 'view/call/prologue.html.php';
?>

<div id="main" class="onecol">
    <ul id="list">
        <li class="item" id="description">
            <img src="<?php if ($call->image instanceof Goteo\Model\Image)
    echo $call->logo->getLink(150) ?>" alt="<?php echo $call->user->name ?>" />
                 <?php echo new View('view/call/widget/title.html.php', $this); ?>

            <p><?php echo $call->description ?></p>
        </li>
        <li class="item" id="numbers">
            <?php echo new View('view/call/widget/stats.html.php', $this); ?>
        </li>
        <?php if (!empty($call->sponsors)) : ?>
            <li class="item" id="sponsors">
                <span><?php echo Text::get('node-header-sponsorby') ?></span>
                <?php foreach ($call->sponsors as $sponsor) : ?>
                    <div>
                        <a href="<?php echo $sponsor->url ?>" target="_blank" title="<?php echo $sponsor->name ?>"><img src="<?php if ($sponsor->image instanceof \Goteo\Model\Image)
                echo $sponsor->image->getLink(130); ?>" alt="<?php echo $sponsor->name ?>" /></a>
                    </div>
                <?php endforeach; ?>
            </li>
        <?php endif; ?>
    </ul>
</div>

<a href="<?php echo SITE_URL ?>/service/resources" id="capital" target="_blank">Goteo.org</a>

<?php include 'view/epilogue.html.php' ?>