<?php
use Goteo\Library\Text;

$call = $this['call'];
?>
<script type="text/javascript">
	$(function(){
		$('#sponsors').slides({
			container: 'slides_container',
			effect: 'fade',
			crossfade: false,
			fadeSpeed: 350,
			play: 5000,
			pause: 1
		});
	});
</script>
<div id="sponsors">
     <h8 class="title"><?php echo Text::get('node-header-sponsorby') ?></h8>
	<div class="slides_container">
		<?php $i = 1; foreach ($call->sponsors as $sponsor) : ?>
		<div class="sponsor" id="call-sponsor-<?php echo $i ?>">
			<a href="<?php echo $sponsor->url ?>" title="<?php echo $sponsor->name ?>" target="_blank" rel="nofollow"><img src="<?php echo $sponsor->image->getLink(150, 85) ?>" alt="<?php echo htmlspecialchars($sponsor->name) ?>" /></a>
		</div>
		<?php $i++; endforeach; ?>
	</div>
	<div class="slidersponsors-ctrl">
		<a class="prev">prev</a>
		<ul class="paginacion"></ul>
		<a class="next">next</a>
	</div>
 </div>
