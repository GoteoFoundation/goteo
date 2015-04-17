<?php
use Goteo\Library\Page,
    Goteo\Model\Node;

$node = $vars['node'];

$pages = Page::getAll(array(), 'es', $node);
?>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Página</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page) : ?>
            <tr>
                <td><a href="/translate/node/<?php echo $node ?>/page/edit/<?php echo $page->id; ?>">[Translate]</a></td>
                <td><?php echo $page->name; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
