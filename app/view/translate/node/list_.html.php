<?php
use Goteo\Library\Page,
    Goteo\Model\Node;

$node = $vars['node'];
?>
<div class="widget board">
    <h3>Contenidos del nodo</h3>
    <ul>
        <li><a href="/translate/node/<?php echo $node ?>/data">Datos del nodo</a></li>
        <li><a href="/translate/node/<?php echo $node ?>/page">Páginas</a></li>
        <li><a href="/translate/node/<?php echo $node ?>/post">Blog</a></li>
        <li><a href="/translate/node/<?php echo $node ?>/banner">Banners</a></li>
    </ul>
</div>
