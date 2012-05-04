<?php
use Goteo\Library\Page,
    Goteo\Model\Node;

$node = $this['node'];
?>
<div class="widget board">
    <h3>Contenidos del nodo</h3>
    <ul>
        <li><a href="/translate/node/<?php echo $node ?>/data">Descripción</a></li>
        <li><a href="/translate/node/<?php echo $node ?>/page">Páginas institucionales</a></li>
        <li><a href="/translate/node/<?php echo $node ?>/post">Entradas Blog</a></li>
        <li><a href="/translate/node/<?php echo $node ?>/banner">Banners</a></li>
    </ul>
</div>
