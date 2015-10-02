<?php
/// esto se usa ???
use Goteo\Library\Page;

$node = $vars['node'];

$pages = Page::getList($node->id);
?>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Página</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page) : ?>
            <tr>
                <td><a href="/translate/pages/edit/<?php echo $page->id; ?>">[Translate]</a></td>
                <td><?php echo $page->name; ?></td>
                <td><?php echo $page->description; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
