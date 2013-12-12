<?php
$share = $this['share'];
?>
<footer>
	<section class="logo">
    	<a href="<?php echo (NODE_ID != GOTEO_NODE) ? NODE_URL : SITE_URL; ?>" class="logo"><img src="/view/bazar/img/logo.svg" /></a>
	</section>
	<section>
	    <h3><a href="<?php echo $share->bazar->url; ?>"><?php echo $share->description; ?></a></h3>
		<nav>    
		    <a href="<?php echo htmlspecialchars($share->bazar_twitter_url) ?>" target="_blank"><img class="twit" src="/view/bazar/img/twitter.svg"></a>
		    <a href="<?php echo htmlspecialchars($share->bazar_facebook_url) ?>" target="_blank"><img class="face" src="/view/bazar/img/facebook.svg"></a>
	    </nav>
	</section>
</footer>
