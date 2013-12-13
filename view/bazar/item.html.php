<?php
use Goteo\Library\Text;

$item = $this['item'];
$item->imgsrc = (!empty($item->img)) ? '/data/images/'.$item->img->name : '/data/images/bazaritem.svg';

$share = $this['share'];
$item_url = str_replace('/bazaar', '/bazaar/'.$item->id,  $share->bazar_url);
$item_title = Text::get('bazar-spread-text', $item->title);
$item_twitter_url = 'http://twitter.com/home?status=' . urlencode($item_title . ': ' . $item_url);
$item_facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($item_url) . '&t=' . urlencode($item_title);

?>
<article class="activable">
	<a class="expand" href="/bazaar/<?php echo $item->id; ?>"></a>
	<div class="caja"><p class="precio"><?php echo $item->amount; ?>&euro;</p></div>
	<img class="item" src="<?php echo $item->imgsrc; ?>" title="<?php echo $item->title; ?>" alt="IMG" title="<?php echo $item->title; ?>"/>
	<p class="desc"><?php echo $item->title; ?><br /><span class="by"><?php echo $item->project->name.'. '.Text::get('regular-by').' '. $item->project->user->name ?></span></p>
	<nav>    
	    <a href="<?php echo htmlspecialchars($item_twitter_url) ?>" target="_blank"><img class="twit" src="/view/bazar/img/twitter.svg"></a>
	    <a href="<?php echo htmlspecialchars($item_facebook_url) ?>" target="_blank"><img class="face" src="/view/bazar/img/facebook.svg"></a>
    </nav>
</article>
