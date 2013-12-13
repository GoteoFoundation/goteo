<?php
use Goteo\Library\Text,
    Goteo\Library\Page,
    Goteo\Core\View,
    Goteo\Model\Image;

$page = $this['page'];

echo new View("view/bazar/prologue.html.php", array('ogmeta'=>$this['ogmeta'], 'title'=>$page->title, 'description'=>$page->description));
echo new View("view/bazar/header.html.php", $this);

echo new View("view/bazar/spread.html.php", array("share"=>$this['share']));
echo new View("view/bazar/item.html.php", array("item"=>$this['item']));

echo new View("view/bazar/footer.html.php", array("share"=>$this['share']));
echo new View("view/bazar/epilogue.html.php");