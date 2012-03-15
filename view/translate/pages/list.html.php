<?php
use Goteo\Library\Page;

$node = (empty($_SESSION['admin_node'])) ? \GOTEO_NODE : $_SESSION['admin_node'];

$pages = Page::getAll($_SESSION['translator_lang'], $_SESSION['admin_node']);
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
                <td><a href="/translate/pages/edit/<?php echo $page->id; ?>">[Edit]</a></td>
                <td><?php echo $page->name; ?></td>
                <td><?php echo $page->description; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
