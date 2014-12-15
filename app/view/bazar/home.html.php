<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image;

$page  = $this['page'];

echo View::get('bazar/prologue.html.php', array('ogmeta'=>$this['ogmeta'], 'metas_seo' => $this['metas_seo'], 'title'=>$page->title, 'description'=>$page->description));
echo View::get('bazar/header.html.php', array('page'=>$this['page']));

if(isset($_SESSION['messages'])) { include __DIR__ . '/../header/message.html.php'; }

echo '<section id="contenedor">';

foreach($this['items'] as $item){
	echo View::get('bazar/item.html.php', array("item"=>$item, "share"=>$this['share']));
}

echo '</section>';

echo View::get('bazar/footer.html.php', array("share"=>$this['share'], 'text'=>$page->txt3));
echo View::get('bazar/epilogue.html.php');
