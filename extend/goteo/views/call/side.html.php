<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$call = $vars['call'];
$filter = $vars['filter'];
?>
<div id="side">
    <p class="block"><?php echo $call->subtitle ?></p>

<?php if ($call->status == 3) : //inscripcion ?>
    <?php if (!empty($call->amount)) : ?>
    <dl class="">
        <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
        <dd class="money"><?php echo \amount_format($call->amount) ?></dd>
    </dl>
    <?php else : ?>
    <dl class="block category">
        <dt><?php echo Text::get('call-splash-resources-header') ?></dt>
        <dd><?php echo $call->resources ?></dd>
    </dl>
    <?php endif; ?>
    <dl class="expires">
        <dt><?php echo Text::get('call-splash-valid_until-header') ?></dt>
        <dd><strong><?php echo $call->until['day'] ?></strong> <?php echo $call->until['month'] ?> / <?php echo $call->until['year'] ?></dd>
    </dl>
    <dl class="applied">
        <dt><?php echo Text::get('call-splash-applied_projects-header') ?></dt>
        <dd><?php echo $call->applied ?></dd>
    </dl>
<?php elseif (!empty($call->amount)) : ?>
    <dl class="">
        <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
        <dd class="money light"><?php echo \amount_format($call->amount) ?></dd>
    </dl>
    <dl class="">
        <dt><?php echo Text::get('call-splash-remain_budget-header') ?></dt>
        <dd class="money"><?php echo \amount_format($call->rest) ?></dd>
    </dl>
<?php endif; ?>
    <dl class="block return">
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
    <dl class="block category">
        <dt><?php echo Text::get('call-splash-categories-header') ?></dt>
        <dd><?php
            $c = 1;
            foreach ($call->categories as $catId => $catName) {
                if ($catId == $filter) {
                    echo '<a href="/call/'.$call->id.'/projects" class="current">'.$catName.'</a>';
                } else {
                    echo '<a href="/call/'.$call->id.'/projects?filter='.$catId.'">'.$catName.'</a>';
                }
                if ($c < count($call->categories))
                    echo ', ';
                $c++;
            }
        ?></dd>
    </dl>

<?php if (!empty($call->user->webs[0]->url)) : $web = $call->user->webs[0];?>
    <dl class="mobile_contact">
        <dt><?php echo Text::get('call-splash-more_info-header') ?></dt>
        <dd><a href="<?php echo $web->url ?>"><?php echo Text::cutUrlParams($web->url) ?></a></dd>
    </dl>
<?php endif; ?>

<?php if (!empty($call->call_location)) : ?>
    <dl class="block">
        <dd class="location"><?php echo Text::GmapsLink($call->call_location); ?></dd>
    </dl>
<?php endif; ?>

<?php if (!empty($call->dossier)) : ?>
    <dl class="">
        <dd><a class="red" href="<?php echo $call->dossier ?>" target="_blank"><?php echo Text::get('call-splash-dossier-link') ?></a></dd>
    </dl>
<?php endif; ?>

    <dl class="block category">
        <dt><?php echo Text::get('call-side-contact_header') ?></dt>
        <dd><a href="mailto:<?php echo $call->user->email ?>" target="_blank"><?php echo $call->user->email ?></a></dd>
    </dl>

</div>
