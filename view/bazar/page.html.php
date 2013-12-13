<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image;

$item  = $this['item'];
$page  = $this['page'];
$share = $this['share'];

echo new View("view/bazar/prologue.html.php", array('ogmeta'=>$this['ogmeta'], 'title'=>$page->title, 'description'=>$page->description));
?>
<header>
	<a href="<?php echo $page->url; ?>" class="logo"><img src="<?php echo $item->imgsrc; ?>" alt="IMG" title="<?php echo $item->name; ?>"></a>
	<nav>
		<h1><a href="<?php echo $page->url; ?>"><?php echo $page->name; ?></a></h1>
		<h2><a href="<?php echo $page->url; ?>"><?php echo $page->description; ?></a></h2>
		<div id="encabezado"><?php echo $item->description.'<br /><span class="by">'.$item->project->name.'. '.Text::get('regular-by').' '. $item->project->user->name.'</span>'; ?></div>
	</nav>
</header>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

<section id="contenedor"><hr />

<article class="header">
	<div class="caja"><p class="precio"><?php echo $item->amount; ?>&euro;</p></div>
	<nav>    
	    <a href="<?php echo htmlspecialchars($share->item_twitter_url) ?>" target="_blank"><img class="twit" src="/view/bazar/img/twitter.svg"></a>
	    <a href="<?php echo htmlspecialchars($share->item_facebook_url) ?>" target="_blank"><img class="face" src="/view/bazar/img/facebook.svg"></a>
    </nav>
</article>

<?php
echo new View("view/bazar/form.html.php", $this);

// echo new View("view/bazar/proj.html.php");
// echo new View("view/bazar/slide.html.php");

echo '</section><hr />';

echo new View("view/bazar/footer.html.php", array("share"=>$this['share']));
echo new View("view/bazar/epilogue.html.php");
?>