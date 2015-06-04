<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image;

$page  = $vars['page'];

echo View::get('bazar/prologue.html.php', array('ogmeta'=>$vars['ogmeta'], 'metas_seo' => $vars['metas_seo'], 'title'=>$page->title, 'description'=>$page->description));
echo View::get('bazar/header.html.php', array('page'=>$vars['page']));

if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; }

echo '<section id="contenedor">';

foreach($vars['items'] as $item){
    echo View::get('bazar/item.html.php', array("item"=>$item, "share"=>$vars['share']));
}

echo '</section>';

echo View::get('bazar/footer.html.php', array("share"=>$vars['share'], 'text'=>$page->txt3));
echo View::get('bazar/epilogue.html.php');
