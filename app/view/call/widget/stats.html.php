<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$call = $vars['call'];
?>
<div id="stats">
    <?php if ($call->status == 3) : //inscripcion   ?>
        <div class="row">
            <?php if (!empty($call->amount)) : //con dinero   ?>
                <dl class="block long last">
                    <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
                    <dd class="money"><?php echo \amount_format($call->amount) ?></dd>
                </dl>
            <?php else: ?>
                <dl class="block long category">
                    <dt><?php echo Text::get('call-splash-resources-header') ?></dt>
                    <dd><?php echo $call->resources ?></dd>
                </dl>
            <?php endif; ?>
            <dl class="block long expires">
                <dt><?php echo Text::get('call-splash-valid_until-header') ?></dt>
                <dd><strong><?php echo $call->until['day'] ?></strong> <?php echo $call->until['month'] ?> / <?php echo $call->until['year'] ?></dd>
            </dl>
            <dl class="block last applied">
                <dt><?php echo Text::get('call-splash-applied_projects-header') ?></dt>
                <dd><?php echo $call->applied ?></dd>
            </dl>
        </div>
        <div class="row">

            <dl class="block category">
                <dt><?php echo Text::get('call-splash-categories-header') ?></dt>
                <dd><?php echo implode(', ', $call->categories) ?></dd>
            </dl>
            <dl class="block return">
                <dt><?php echo Text::get('call-splash-icons-header') ?></dt>
                <dd>
                    <ul>
                        <?php foreach ($call->icons as $iconId => $iconName) : ?>
                            <li class="<?php echo $iconId ?> activable">
                                <a class="tipsy" title="<?php echo $iconName ?>" ><?php echo $iconName ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </dd>
            </dl>
            <dl class="block moreinfo last">
                <dt><?php echo Text::get('call-splash-more_info-header') ?></dt>
                <dd>
                    <?php if (!empty($call->pdf)) : ?><a class="red" href="<?php echo $call->pdf ?>" target="_blank"><?php echo Text::get('call-splash-dossier-link') ?></a><?php endif; ?>
                    <?php if (!empty($call->user->webs[0]->url)) : ?><a href="<?php echo $call->user->webs[0]->url ?>" target="_blank"><?php echo Text::cutUrlParams($call->user->webs[0]->url) ?></a><?php endif; ?>
                    <a class="red" href="<?php echo $call->dossier ?>" target="_blank"><?php echo Text::get('call-splash-dossier-link') ?></a>
                    <!-- a class="aqua" href="/call/<?php echo $call->id ?>/terms" ><?php echo Text::get('call-splash-legal-link') ?></a -->
                </dd>
            </dl>
        </div>

        <?php if (!empty($call->call_location)) : ?>
            <dl class="block location">
                <dt><?php echo Text::get('call-splash-location-header') ?></dt>
                <dd><?php echo Text::GmapsLink($call->call_location); ?></dd>
            </dl>
        <?php endif; ?>

        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info" class="button info long" ><?php echo Text::get('call-splash-more_info-button') ?></a>
        <?php if (!$call->expired) : // sigue abierta   ?>
            <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/apply" class="button red join" target="_blank"><?php echo Text::get('call-splash-apply-button') ?></a>
        <?php endif; ?>

    <?php else : //en campaña   ?>
        <div class="row">
            <?php if (!empty($call->amount)) : //con dinero   ?>
                <dl class="block long last">
                    <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
                    <dd class="money light"><?php echo \amount_format($call->amount) ?></dd>
                </dl>
                <dl class="block long">
                    <dt><?php echo Text::get('call-splash-remain_budget-header') ?></dt>
                    <dd class="money"><?php echo \amount_format($call->rest) ?></dd>
                </dl>
            <?php else : // sin dinero, con recursos   ?>
                <dl class="block long return">
                    <dt><?php echo Text::get('call-splash-icons-header') ?></dt>
                    <dd>
                        <ul>
                            <?php foreach ($call->icons as $iconId => $iconName) : ?>
                                <li class="<?php echo $iconId ?> activable">
                                    <a class="tipsy" title="<?php echo $iconName ?>" ><?php echo $iconName ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </dd>
                </dl>
            <?php endif; ?>
            <dl class="block last category">
                <dt><?php echo Text::get('call-splash-categories-header') ?></dt>
                <dd><?php echo implode(', ', $call->categories) ?></dd>
            </dl>
        </div>
        <div class="row">
            <dl class="block selected">
                <dt><?php echo Text::get('call-splash-selected_projects-header') ?></dt>
                <dd><?php echo $call->applied ?></dd>
            </dl>
            <dl class="block processing">
                <dt><?php echo Text::get('call-splash-runing_projects-header') ?></dt>
                <dd><?php echo $call->running_projects ?></dd>
            </dl>
            <dl class="block success">
                <dt><?php echo Text::get('call-splash-success_projects-header') ?></dt>
                <dd><?php echo $call->success_projects ?></dd>
            </dl>
            <?php if (!empty($call->amount)) : //con dinero   ?>
                <dl class="block last return">
                    <dt><?php echo Text::get('call-splash-icons-header') ?></dt>
                    <dd>
                        <ul>
                            <?php foreach ($call->icons as $iconId => $iconName) : ?>
                                <li class="<?php echo $iconId ?> activable">
                                    <a class="tipsy" title="<?php echo $iconName ?>" ><?php echo $iconName ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </dd>
                </dl>
            <?php endif; ?>
        </div>

        <?php if (!empty($call->call_location)) : ?>
            <dl class="block location">
                <dt><?php echo Text::get('call-splash-location-header') ?></dt>
                <dd><?php echo Text::GmapsLink($call->call_location); ?></dd>
            </dl>
        <?php endif; ?>

        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info" class="button info"><?php echo Text::get('call-splash-more_info-button') ?></a>
         <?php if ($call->num_projects > 0) : // solo si tiene proyectos  ?>
        <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/projects" class="button red view"><?php echo Text::get('call-splash-see_projects-button') ?></a>
        <?php endif; ?>

    <?php endif; ?>

</div>
