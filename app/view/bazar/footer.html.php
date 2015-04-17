<?php
$share = $vars['share'];
?>
<hr />
<div id="pie"><?php echo $vars['text']; ?></div>

<footer>
	<section class="logo">
    	<a href="<?php echo SITE_URL; ?>" class="logo"><img src="<?php echo SRC_URL; ?>/view/bazar/img/logo.svg" /></a>
	</section>
	<section class="logo2">
    	<img src="<?php echo SRC_URL; ?>/view/bazar/img/logo.svg" />
	</section>

	<section>
		<nav>
		    <a href="<?php echo htmlspecialchars($share->bazar_twitter_url) ?>" target="_blank"><img class="twit" src="<?php echo SRC_URL; ?>/view/bazar/img/twitter.svg"></a>
		    <a href="<?php echo htmlspecialchars($share->bazar_facebook_url) ?>" target="_blank"><img class="face" src="<?php echo SRC_URL; ?>/view/bazar/img/facebook.svg"></a>
	    </nav>
	</section>
</footer>
