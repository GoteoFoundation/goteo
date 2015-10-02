<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$node = $vars['node'];
?>
<div class="widget board">
    <h3>Traducir Contenidos del nodo <strong><?php echo $node->name; ?></strong></h3>
    <ul>
        <li><a href="/translate/node/<?php echo $node->id ?>/data/edit" target="_blank">Descripción</a></li>
        <li><a href="/translate/node/<?php echo $node->id ?>/page" target="_blank">Páginas institucionales</a></li>
        <li><a href="/translate/node/<?php echo $node->id ?>/post" target="_blank">Entradas Blog</a></li>
        <li><a href="/translate/node/<?php echo $node->id ?>/banner" target="_blank">Banners</a></li>
    </ul>
</div>
