<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image;
	
echo new View("view/bazar/prologue.html.php", array('ogmeta'=>$this['ogmeta']));
echo new View("view/bazar/header.html.php", array('page'=>$this['page']));
//echo new View("view/bazar/share.html.php", array("share"=>$this['share']));

echo '<section id="contenedor">';

foreach($this['items'] as $item){
	echo new View("view/bazar/item.html.php",array("item"=>$item));
}

echo '</section><hr />';

echo new View("view/bazar/footer.html.php", array("share"=>$this['share']));
echo new View("view/bazar/epilogue.html.php");