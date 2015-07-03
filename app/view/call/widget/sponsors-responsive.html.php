<?php
use Goteo\Library\Text;

$call = $vars['call'];
?>

<div id="sponsors-responsive">
     <h8 class="title"><?php echo Text::get('node-header-sponsorby') ?></h8>
	<div class="slides_container" style="margin-top:15px; margin-left:15px;">
		<?php $i = 1; foreach ($call->sponsors as $sponsor) : ?>
		<div class="sponsor" id="call-sponsor-<?php echo $i ?>">
			<a href="<?php echo $sponsor->url ?>" title="<?php echo $sponsor->name ?>" target="_blank" rel="nofollow"><img src="<?php echo $sponsor->image->getLink(150, 85) ?>" style="max-width:100%;" alt="<?php echo htmlspecialchars($sponsor->name); ?>" /></a>
		</div>
		<?php $i++; endforeach; ?>
	</div>
 </div>
