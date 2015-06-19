<?php
use Goteo\Library\Text;

$item = $vars['item'];

$share = $vars['share'];
$item_url = str_replace('/bazaar', '/bazaar/'.$item->id,  $share->bazar_url);
$item_title = Text::get('bazar-spread-text', $item->title);
$item_twitter_url = 'http://twitter.com/home?status=' . urlencode($item_title . ': ' . $item_url);
$item_facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($item_url) . '&t=' . urlencode($item_title);

?>
<article class="activable">
	<a class="expand" href="/bazaar/<?php echo $item->id; ?>"></a>
	<div class="caja"><p class="precio"><?php echo $item->amount; ?>&euro;</p></div>
	<img class="item" src="<?php echo $item->imgsrc; ?>" title="<?php echo htmlspecialchars($item->title) ?>" alt="IMG" />
	<p class="desc"><?php echo $item->title; ?></p>
	<div id="proj-name"><span><?php echo $item->project->name.'<br />'.Text::get('regular-by').' '. $item->project->user->name ?></span></div>
	<nav>
	    <a href="<?php echo htmlspecialchars($item_twitter_url) ?>" target="_blank"><img class="twit" src="<?php echo SRC_URL; ?>/view/bazar/img/twitter.svg"></a>
	    <a href="<?php echo htmlspecialchars($item_facebook_url) ?>" target="_blank"><img class="face" src="<?php echo SRC_URL; ?>/view/bazar/img/facebook.svg"></a>
    </nav>
</article>
