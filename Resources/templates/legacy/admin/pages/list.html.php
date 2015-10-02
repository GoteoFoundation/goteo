<?php
use Goteo\Library\Text,
    Goteo\Model\User\Translate;

$node = $vars['node'];
$transNode = Translate::is_legal($_SESSION['user']->id, $node, 'node') ? true : false;
$translator = ( isset($_SESSION['user']->roles['translator']) ) ? true : false;
?>
<?php if ($node == \GOTEO_NODE) : ?>
<a href="/admin/pages/add" class="button">Nueva P&aacute;gina</a>
<?php elseif (!empty($node)) : ?>
<a href="/translate/node/<?php echo $node; ?>/page/list" class="button">Traducir páginas</a>
<?php endif; ?>

<div class="widget board">
    <?php if (!empty($vars['pages'])) : ?>
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
            <?php foreach ($vars['pages'] as $page) : ?>
            <tr>
                <td><a href="/admin/pages/edit/<?php echo $page->id; ?>">[Editar]</a></td>
                <td><?php echo $page->name; ?></td>
                <td><?php echo $page->description; ?></td>
                <td><a href="<?php echo $page->url; ?>" target="_blank">[Ver]</a></td>
                <td>
                <?php if ($translator && $node == \GOTEO_NODE) : ?>
                    <a href="/translate/pages/edit/<?php echo $page->id; ?>" >[Traducir]</a>
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
