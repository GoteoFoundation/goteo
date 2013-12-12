<?php
use Goteo\Library\Text;

$share = $this['share'];
?>

<div>

	<p class="spread">Difunde este articulo</p>

	<ul class="share">
	  <li class="twitter"><a href="<?php echo htmlspecialchars($share->item_twitter_url) ?>" target="_blank"><?php echo Text::get('spread-twitter'); ?></a></li>
	  <li class="facebook"><a href="<?php echo htmlspecialchars($share->item_facebook_url) ?>" target="_blank"><?php echo Text::get('spread-facebook'); ?></a></li>
	</ul>

	<p class="thanks">Mensaje de agradecimiento</p>

</div>