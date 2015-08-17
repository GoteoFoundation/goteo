<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'splash';

$call = $vars['call'];

include __DIR__ . '/prologue.html.php';
?>

<div id="main" class="onecol">
    <ul id="list">
        <li class="item" id="description">
            <img src="<?php if ($call->image instanceof Goteo\Model\Image) echo $call->logo->getLink(150, 85) ?>" alt="<?php echo $call->user->name ?>" />
                <h2 class="title"><?php echo Text::get('call-splash-campaign_title') ?><br /><?php echo $call->name ?></h2>

                <?php if ($call->status == 3) : //inscripcion ?>
                <p class="subtitle red"><?php echo Text::get('call-splash-searching_projects') ?></p>
                <?php elseif (!empty($call->amount)) : //en campaña con dinero ?>
                <!--p class="subtitle"><?php echo Text::html('call-splash-invest_explain', $call->user->name) ?></p-->
                <?php else : //en campaña sin dinero, con recursos ?>
                <p class="subtitle"><?php echo Text::recorta($call->resources, 200) ?></p>
                <?php endif; ?>

            <p><?php echo $call->description ?></p>
        </li>
        <li class="item" id="numbers">
            <?php echo View::get('call/widget/stats.html.php', $vars); ?>
        </li>
        <?php if (!empty($call->sponsors)) : ?>
            <li class="item" id="sponsors">
                <span><?php echo Text::get('node-header-sponsorby') ?></span>
                <?php foreach ($call->sponsors as $sponsor) : ?>
                    <div>
                        <a href="<?php echo $sponsor->url ?>" target="_blank" title="<?php echo $sponsor->name ?>"><img src="<?php if ($sponsor->image instanceof \Goteo\Model\Image)
                echo $sponsor->image->getLink(150, 85); ?>" alt="<?php echo $sponsor->name ?>" /></a>
                    </div>
                <?php endforeach; ?>
            </li>
        <?php endif; ?>
    </ul>
</div>

<a href="<?php echo SITE_URL ?>/service/resources" id="capital" target="_blank">Goteo.org</a>

<?php include GOTEO_WEB_PATH . 'view/epilogue.html.php' ?>
