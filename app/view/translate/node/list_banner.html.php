<?php
use Goteo\Model\Node,
    Goteo\Model\Banner;

$nodes = Node::getList();
$node = $vars['node'];

$banners = Banner::getList($node);
?>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Banner</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($banners as $banner) : ?>
            <tr>
                <td><a href="/translate/node/<?php echo $node ?>/banner/edit/<?php echo $banner->id; ?>">[Translate]</a></td>
                <td><?php echo $banner->title; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
