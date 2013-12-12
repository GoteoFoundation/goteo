<?php
$share = $this['share'];
?>
<footer>
    <h3><?php echo $share->description; ?></h3>
    <a href="<?php echo (NODE_ID != GOTEO_NODE) ? NODE_URL : SITE_URL; ?>"><img src="/view/bazar/img/logo.svg" /></a>
    <a href="<?php echo htmlspecialchars($share->bazar_twitter_url) ?>" target="_blank"><img class="face" src="/view/bazar/img/facebook.svg"></a>
    <a href="<?php echo htmlspecialchars($share->bazar_facebook_url) ?>" target="_blank"><img class="twit" src="/view/bazar/img/twitter.svg"></a>
</footer>
