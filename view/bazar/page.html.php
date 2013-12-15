<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image;

$item  = $this['item'];
$page  = $this['page'];
$share = $this['share'];

echo new View("view/bazar/prologue.html.php", array('ogmeta'=>$this['ogmeta'], 'title'=>$page->title, 'description'=>$page->description));
echo new View("view/bazar/header.html.php", array('page'=>$this['page']));

if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; }
?>

<section id="contenedor">

<article class="header top">
	<div class="caja"><p class="precio"><?php echo $item->amount; ?>&euro;</p></div>
	<img class="item" src="<?php echo $item->imgsrc; ?>" title="<?php echo $item->title; ?>" alt="IMG" title="<?php echo $item->title; ?>"/>
	<nav>
	  <a href="<?php echo htmlspecialchars($share->item_facebook_url) ?>" target="_blank" title="<?php echo Text::get('spread-facebook'); ?>"><img class="face" src="/view/bazar/img/facebook.svg"></a>
	  <a href="<?php echo htmlspecialchars($share->item_twitter_url) ?>" target="_blank" title="<?php echo Text::get('spread-twitter'); ?>"><img class="twit" src="/view/bazar/img/twitter.svg"></a>
	</nav>
</article>
<article class="header prod">
	<div id="prod-description"><?php echo $item->description; ?></div>
	<div id="proj-name"><a href="/project/<?php echo $item->project->id; ?>" target="_blank"><?php echo $item->project->name.'<br />'.Text::get('regular-by').' '. $item->project->user->name; ?></a></div>
</article>

<?php echo new View("view/bazar/form.html.php", $this); ?>

</section>

<?php
echo new View("view/bazar/footer.html.php", array("share"=>$this['share'], 'text'=>$page->txt2));
echo new View("view/bazar/epilogue.html.php");
?>