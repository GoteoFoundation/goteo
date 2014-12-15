<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image;

$page  = $this['page'];

echo new View("view/bazar/prologue.html.php", array('ogmeta'=>$this['ogmeta'], 'metas_seo' => $this['metas_seo'], 'title'=>$page->title, 'description'=>$page->description));
echo new View("view/bazar/header.html.php", array('page'=>$this['page']));

if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; }

echo '<section id="contenedor">';

foreach($this['items'] as $item){
	echo new View("view/bazar/item.html.php",array("item"=>$item, "share"=>$this['share']));
}

echo '</section>';

echo new View("view/bazar/footer.html.php", array("share"=>$this['share'], 'text'=>$page->txt3));
echo new View("view/bazar/epilogue.html.php");