<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image;

$item = $this['item'];

echo new View("view/bazar/prologue.html.php", array('ogmeta'=>$this['ogmeta']));
echo new View("view/bazar/header.html.php", $this);
?>

<section id="contenedor">
<article class="header">
	<div class="caja"><p class="precio"><?php echo $item->amount; ?>&euro;</p></div>
</article>

<?php
echo new View("view/bazar/form.html.php", $this);

// echo new View("view/bazar/proj.html.php");
// echo new View("view/bazar/slide.html.php");

echo '</section><hr />';

echo new View("view/bazar/footer.html.php", array("share"=>$this['share']));
echo new View("view/bazar/epilogue.html.php");
?>