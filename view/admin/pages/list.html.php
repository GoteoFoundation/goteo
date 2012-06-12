<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ACL::check('/translate') ? true : false;
$node = $this['node'];
$transNode = ACL::check('/translate/node/'.$node) ? true : false;
?>
<?php if (!isset($_SESSION['admin_node'])) : ?>
<a href="/admin/pages/add" class="button">Nueva P&aacute;gina</a>
<?php endif; ?>

<div class="widget board">
    <?php if (!empty($this['pages'])) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Página</th>
                <th>Descripción</th>
                <th><!-- Abrir --></th>
                <th><!-- Traducir --></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this['pages'] as $page) : ?>
            <tr>
                <td><a href="/admin/pages/edit/<?php echo $page->id; ?>">[Editar]</a></td>
                <td><?php echo $page->name; ?></td>
                <td><?php echo $page->description; ?></td>
                <td><a href="<?php echo $page->url; ?>" target="_blank">[Ver]</a></td>
                <td>
                <?php if ($translator && $node == \GOTEO_NODE) : ?>
                <a href="/translate/pages/edit/<?php echo $page->id; ?>" >[Traducir]</a>
                <?php endif; ?>
                <?php if ($transNode && $node != \GOTEO_NODE) : ?>
                <a href="/translate/node/<?php echo $node ?>/page/edit/<?php echo $page->id; ?>" target="_blank">[Traducir]</a>
                <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>