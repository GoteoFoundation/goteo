<?php
use Goteo\Library\Text,
    Goteo\Library\Page,
    Goteo\Core\View,
    Goteo\Model\Image;

$page = $vars['page'];
$share = $vars['share'];
$item = $vars['item'];

echo View::get('bazar/prologue.html.php', array('ogmeta'=>$vars['ogmeta'], 'title'=>$page->title, 'description'=>$page->description));
echo View::get('bazar/header.html.php', array('page'=>$vars['page']));

if(isset($_SESSION['messages'])) { include __DIR__ . '/../header/message.html.php'; }
?>

<section id="contenedor">

	<br clear="all" />

	<article class="spread">
		<p><?php echo Text::get('bazar-thanks'); ?></p>
	</article>

	<article class="spread">
		<ul class="share">
		  <li class="twitter"><a href="<?php echo htmlspecialchars($share->item_twitter_url) ?>" target="_blank"><?php echo Text::get('spread-twitter'); ?></a></li>
		  <li class="facebook"><a href="<?php echo htmlspecialchars($share->item_facebook_url) ?>" target="_blank"><?php echo Text::get('spread-facebook'); ?></a></li>
		</ul>
	</article>

	<article class="header top">
		<div class="caja"><p class="precio"><?php echo $item->amount; ?>&euro;</p></div>
		<img class="item" src="<?php echo $item->imgsrc; ?>" title="<?php echo htmlspecialchars($item->title); ?>" alt="IMG" />
	</article>
	<article class="header prod">
		<div id="prod-description"><?php echo $item->description; ?></div>
		<div id="proj-name"><a href="/project/<?php echo $item->project->id; ?>" target="_blank"><?php echo $item->project->name.'<br />'.Text::get('regular-by').' '. $item->project->user->name; ?></a></div>
	</article>

</section>

<?php
echo View::get('bazar/footer.html.php', array("share"=>$vars['share'], 'text'=>$page->txt3));
echo View::get('bazar/epilogue.html.php');
