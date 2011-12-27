<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$bodyClass = 'splash';

$call = $this['call'];

if ($call->status == 3) {
    $until = strtotime($call->until);
    $until_day = date('d', $until);
    $until_month = strftime('%B', $until);
    $until_month = ucfirst(substr($until_month, 0, 3));
    $until_year = date('Y', $until);
}

include 'view/call/prologue-splash.html.php';
?>
	
	<img id="bgimage" src="<?php echo $call->image->getLink(2000, 2000) ?>" alt="<?php echo $call->name ?>" />

	<div id="main" class="onecol">
		<ul id="list">
			<li class="item" id="description">
				<img src="<?php echo $call->logo->getLink(155, 200) ?>" alt="<?php echo $call->user->name ?>" />
                <h2 class="title"><?php echo Text::get('call-splash-campaign_title') ?><br /><?php echo $call->name ?></h2>
                <?php if ($call->status == 3) : //inscripcion ?>
                <p class="subtitle red"><?php echo Text::get('call-splash-searching_projects') ?></p>
                <?php else : //en campaña ?>
				<p class="subtitle"><?php echo Text::get('call-splash-invest_explain', $call->user->name) ?></p>
                <?php endif; ?>
				<p><?php echo $call->description ?></p>
			</li>
			<li class="item" id="numbers">
            <?php if ($call->status == 3) : //inscripcion ?>
				<div class="row">
                    <dl class="block long last">
                        <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
                        <dd class="money"><?php echo \amount_format($call->amount) ?> <span class="euro">&euro;</span></dd>
                    </dl>
                    <dl class="block long expires">
                        <dt><?php echo Text::get('call-splash-valid_until-header') ?></dt>
						<dd><strong><?php echo $until_day ?></strong> <?php echo $until_month ?> / <?php echo $until_year ?></dd>
                    </dl>
                    <dl class="block last selected">
                        <dt><?php echo Text::get('call-splash-applied_projects-header') ?></dt>
                        <dd><?php echo count($call->projects) ?></dd>
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
                                <?php foreach ($call->icons as $iconId=>$iconName) : ?>
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
                            <a class="red" href="<?php echo $call->pdf ?>" target="_blank"><?php echo Text::get('call-splash-dossier-link') ?></a>
                            <a href="<?php echo $call->user->webs[0]->url ?>" target="_blank"><?php echo preg_replace( '^http(?<https>s)?://^', '', $call->user->webs[0]->url ) ?></a>
                            <a class="aqua" href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/terms" target="_blank"><?php echo Text::get('call-splash-legal-link') ?></a>
                        </dd>
                    </dl>
				</div>
            <?php else : //en campaña ?>
				<div class="row">
                    <dl class="block long last">
                        <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
                        <dd class="money light"><?php echo \amount_format($call->amount) ?> <span class="euro">&euro;</span></dd>
                    </dl>
                    <dl class="block long">
                        <dt><?php echo Text::get('call-splash-remain_budget-header') ?></dt>
                        <dd class="money"><?php echo \amount_format($call->rest) ?> <span class="euro">&euro;</span></dd>
                    </dl>
                    <dl class="block last category">
                        <dt><?php echo Text::get('call-splash-categories-header') ?></dt>
                        <dd><?php echo implode(', ', $call->categories) ?></dd>
                    </dl>
                </div>
				<div class="row">
                    <dl class="block selected">
                        <dt><?php echo Text::get('call-splash-selected_projects-header') ?></dt>
                        <dd><?php echo count($call->projects) ?></dd>
                    </dl>
                    <dl class="block processing">
                        <dt><?php echo Text::get('call-splash-runing_projects-header') ?></dt>
                        <dd><?php echo $call->runing_projects ?></dd>
                    </dl>
                    <dl class="block success">
                        <dt><?php echo Text::get('call-splash-success_projects-header') ?></dt>
                        <dd><?php echo $call->success_projects ?></dd>
                    </dl>
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
                </div>
            <?php endif; ?>

				<dl class="block location">
					<dt><?php echo Text::get('call-splash-location-header') ?></dt>
					<dd><?php echo Text::GmapsLink($call->call_location); ?></dd>
				</dl>

            <?php if ($call->status == 3) : //inscripcion ?>
				<a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info" class="button aqua info long" target="_blank"><?php echo Text::get('call-splash-more_info-button') ?></a>
                <?php if (!$call->expired) : // sigue abierta ?>
                    <a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/apply" class="button red join" target="_blank"><?php echo Text::get('call-splash-apply-button') ?></a>
                <?php endif; ?>
            <?php else : // ver proyectos ?>
				<a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/info" class="button aqua info" target="_blank"><?php echo Text::get('call-splash-more_info-button') ?></a>
				<a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/projects" class="button red view" target="_blank"><?php echo Text::get('call-splash-see_projects-button') ?></a>
            <?php endif; ?>
			</li>
		</ul>
    </div>

	<a href="<?php echo SITE_URL ?>/service/resources" id="capital" target="_blank"><?php echo Text::get('footer-service-resources') ?></a>
    
<?php include 'view/epilogue.html.php' ?>