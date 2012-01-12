<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$call = $this['call'];

if ($call->status == 3) {
    $until = strtotime($call->until);
    $until_day = date('d', $until);
    $until_month = strftime('%B', $until);
    $until_month = ucfirst(substr($until_month, 0, 3));
    $until_year = date('Y', $until);
}

?>
<div id="side">
	<a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>"><img src="<?php echo $call->logo->getLink(155, 200) ?>" alt="<?php echo $call->user->name ?>" class="logo" /></a>
	<p class="block"><?php echo $call->subtitle ?></p>
    
<?php if ($call->status == 3) : //inscripcion ?>
    <?php if (!empty($call->amount)) : ?>
    <dl class="">
        <dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
        <dd class="money"><?php echo \amount_format($call->amount) ?> <span class="euro">&euro;</span></dd>
    </dl>
    <?php else : ?>
	<dl class="block category">
		<dt><?php echo Text::get('call-splash-resources-header') ?></dt>
		<dd><?php echo $call->resources ?></dd>
	</dl>
    <?php endif; ?>
    <dl class="expires">
        <dt><?php echo Text::get('call-splash-valid_until-header') ?></dt>
        <dd><strong><?php echo $until_day ?></strong> <?php echo $until_month ?> / <?php echo $until_year ?></dd>
    </dl>
    <dl class="applied">
        <dt><?php echo Text::get('call-splash-applied_projects-header') ?></dt>
        <dd><?php echo count($call->projects) ?></dd>
    </dl>
<?php else : //en campaña ?>
    <?php if (!empty($call->amount)) : ?>
	<dl class="">
		<dt><?php echo Text::get('call-splash-whole_budget-header') ?></dt>
		<dd class="money light"><?php echo \amount_format($call->amount) ?> <span class="euro">&euro;</span></dd>
	</dl>
	<dl class="">
		<dt><?php echo Text::get('call-splash-remain_budget-header') ?></dt>
		<dd class="money"><?php echo \amount_format($call->rest) ?> <span class="euro">&euro;</span></dd>
	</dl>
    <?php endif; ?>
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
		<dd><?php echo implode(', ', $call->categories) ?></dd>
	</dl>
    
<?php if ($call->status == 3) : //inscripcion ?>
	<?php if (!empty($call->pdf)) : ?>
    <dl class="">
		<dt><?php echo Text::get('call-splash-more_info-header') ?></dt>
		<dd><a class="red" href="<?php echo $call->pdf ?>" target="_blank"><?php echo Text::get('call-splash-dossier-link') ?></a></dd>
	</dl>
    <?php endif; ?>
<?php endif; ?>

    <?php if (!empty($call->user->webs[0]->url)) : ?>
	<dl class="">
		<dt>Web</dt>
		<dd><a href="<?php echo $call->user->webs[0]->url ?>"><?php echo preg_replace( '^http(?<https>s)?://^', '', $call->user->webs[0]->url ) ?></a></dd>
	</dl>
    <?php endif; ?>
    <?php if (!empty($call->call_location)) : ?>
	<dl class="block">
		<dd class="location"><?php echo Text::GmapsLink($call->call_location); ?></dd>
	</dl>
    <?php endif; ?>
	<dl class="block category">
		<dd><a href="<?php echo SITE_URL ?>/call/<?php echo $call->id ?>/terms"><?php echo Text::get('call-splash-legal-link') ?></a></dd>
	</dl>
	<a href="<?php echo SITE_URL ?>/service/resources" id="capital" target="_blank"><?php echo Text::get('footer-service-resources') ?></a>
	
        <a href="#" id="go-up"><?php echo Text::get('regular-go_up') ?></a>
</div>