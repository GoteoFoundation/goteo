<?php
use Goteo\Library\Text;

$share = $vars['share'];
?>
<section class="redes">
  <a href="<?php echo htmlspecialchars($share->bazar_twitter_url) ?>" target="_blank" title="<?php echo Text::get('spread-twitter'); ?>"><img class="face" src="img/facebook.svg"></a>
  <a href="<?php echo htmlspecialchars($share->bazar_facebook_url) ?>" target="_blank" title="<?php echo Text::get('spread-facebook'); ?>"><img class="twit" src="img/twitter.svg"></a>
</section>
