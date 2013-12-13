<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image;

$item = $this['item'];

echo new View("view/bazar/prologue.html.php", array('ogmeta'=>$this['ogmeta']));
echo new View("view/bazar/header.html.php", $this);
?>
<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

<section id="contenedor"><hr />

<article class="header">
	<div class="caja"><p class="precio"><?php echo $item->amount; ?>&euro;</p></div>
	<nav>    
	    <a href="<?php echo htmlspecialchars($item_twitter_url) ?>" target="_blank"><img class="twit" src="/view/bazar/img/twitter.svg"></a>
	    <a href="<?php echo htmlspecialchars($item_facebook_url) ?>" target="_blank"><img class="face" src="/view/bazar/img/facebook.svg"></a>
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