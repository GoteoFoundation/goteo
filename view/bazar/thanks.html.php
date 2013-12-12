<?php
use Goteo\Library\Text,
    Goteo\Library\Page,
    Goteo\Core\View,
    Goteo\Model\Image;
	
echo new View("view/bazar/prologue.html.php");
echo new View("view/bazar/header.html.php", array('page'=>$this['page']));

echo new View("view/bazar/name.html.php",array("item"=>$this['item']));
echo new View("view/bazar/spread.html.php",array("share"=>$this['share']));

echo new View("view/bazar/footer.html.php",array("share"=>$this['share']));
echo new View("view/bazar/epilogue.html.php");