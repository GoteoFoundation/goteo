<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model;

$call = $this['call'];

// fecha hasta
if ($call->status == 3) {
    $until = strtotime($call->until);
    $until_day = date('d', $until);
    $until_month = strftime('%B', $until);
    $until_month = ucfirst(substr($until_month, 0, 3));
    $until_year = date('Y', $until);
}

$the_logo = empty($call->logo) ? 1 : $call->logo;
$call->logo = Model\Image::get($the_logo);
$the_image = empty($call->image) ? 1 : $call->image;
$call->image = Model\Image::get($the_image);

$call->categories = Model\Call\Category::getNames($call->id);
$call->icons = Model\Call\Icon::getNames($call->id);

?>

<div class="call activable">
	<a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>" class="expand"<?php echo $blank; ?>></a>

    <div class="image">
        <?php if ($call->status == 3) : //inscripcion ?>
            <div class="tagmark red">Convocatoria abierta</div>
        <?php else : // campaña?>
            <div class="tagmark aqua">Campa&ntilde;a activa!</div>
        <?php endif ?>

        <?php if (!empty($call->image)): ?>
        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>"><img alt="<?php echo $call->name ?>" src="<?php echo $call->image->getLink(211, 230, true) ?>" /></a>
        <?php endif ?>
    </div>

    <div class="name">
        <?php if (!empty($call->logo)): ?>
        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>"><img alt="<?php echo $call->name ?>" src="<?php echo $call->logo->getLink(120) ?>" /></a>
        <?php endif ?>

        <h3><a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>"<?php echo $blank; ?>><?php echo Text::get('call-splash-campaign_title') ?><br /><?php echo $call->name ?></a></h3>
    </div>

    <div class="data">
        <ul>
            <li class="item" id="description">
                <?php if ($call->status == 3) : //inscripcion ?>
                <p class="subtitle red"><?php echo Text::get('call-splash-searching_projects') ?></p>
                <?php elseif (!empty($call->amount)) : //en campaña con dinero ?>
                <p class="subtitle"><?php echo Text::get('call-splash-invest_explain', $call->user->name) ?></p>
                <?php else : //en campaña sin dinero, con recursos ?>
                <p class="subtitle"><?php echo Text::get('call-splash-resources_explain') ?></p>
                <?php endif; ?>
            </li>

            <li class="item" id="numbers">
                <?php if ($call->status == 3) : //inscripcion ?>
                    <div class="row">
                    <?php if (!empty($call->amount)) : //con dinero ?>
                        <dl class="block long">
                            <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
                            <dd class="money"><?php echo \amount_format($call->amount) ?> <span class="euro">&euro;</span></dd>
                        </dl>
                    <?php else: ?>
                        <dl class="block long resources">
                            <dt><?php echo Text::get('call-splash-resources-header') ?></dt>
                            <dd><?php echo Text::recorta($call->resources, 100) ?></dd>
                        </dl>
                    <?php endif; ?>
                        <dl class="block long expires">
                            <dt><?php echo Text::get('call-splash-valid_until-header') ?></dt>
                            <dd><strong><?php echo $until_day ?></strong> <?php echo $until_month ?> / <?php echo $until_year ?></dd>
                        </dl>
                        <dl class="block last applied">
                            <dt><?php echo Text::get('call-splash-applied_projects-header') ?></dt>
                            <dd><?php echo count($call->projects) ?></dd>
                        </dl>
                    </div>
                <?php else : //en campaña ?>
                    <div class="row">
                    <?php if (!empty($call->amount)) : //con dinero ?>
                        <dl class="block long">
                            <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
                            <dd class="money light"><?php echo \amount_format($call->amount) ?> <span class="euro">&euro;</span></dd>
                        </dl>
                        <dl class="block long">
                            <dt><?php echo Text::get('call-splash-remain_budget-header') ?></dt>
                            <dd class="money"><?php echo \amount_format($call->rest) ?> <span class="euro">&euro;</span></dd>
                        </dl>
                    <?php else : // sin dinero, con recursos ?>
                        <dl class="block resources longer">
                            <dt><?php echo Text::get('call-splash-resources-header') ?></dt>
                            <dd><?php echo Text::recorta($call->resources, 100) ?></dd>
                        </dl>
                    <?php endif; ?>
                        <dl class="block last selected">
                            <dt><?php echo Text::get('call-splash-selected_projects-header') ?></dt>
                            <dd><?php echo count($call->projects) ?></dd>
                        </dl>
                    </div>
                <?php endif; ?>

                <?php if (!empty($call->call_location)) : ?>
                <dl class="block long location">
                    <dt><?php echo Text::get('call-splash-location-header') ?></dt>
                    <dd><?php echo Text::GmapsLink($call->call_location); ?></dd>
                </dl>
                <?php endif; ?>

                <?php if (!empty($call->icons)) : ?>
                <dl class="block last return">
                    <dt><?php echo Text::get('call-splash-icons-header') ?></dt>
                    <dd>
                        <ul>
                            <?php foreach ($call->icons as $iconId=>$iconName) : ?>
                            <li class="<?php echo $iconId ?> activable">
                                <a class="tipsy" title="<?php echo $iconName ?>" ><?php echo $iconName ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </dd>
                </dl>
                <?php endif; ?>
                
            </li>
        </ul>
    </div>

</div>
