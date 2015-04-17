<?php

use Goteo\Library\Text,
    Goteo\Model\User\Translate;

$node = $vars['node'];
$transNode = Translate::is_legal($_SESSION['user']->id, $node, 'node') ? true : false;
$translator = ( isset($_SESSION['user']->roles['translator']) ) ? true : false;
?>
<a href="/admin/banners/add" class="button">Nuevo banner</a>
<?php if (!empty($node) && $node != \GOTEO_NODE) : ?>
<a href="/translate/node/<?php echo $node; ?>/banner/list" class="button">Traducir banners</a>
<?php endif; ?>

<div class="widget board">
    <?php if (!empty($vars['bannered'])) : ?>
    <table>
        <thead>
            <tr>
                <th><?php echo ($node == \GOTEO_NODE) ? 'Proyecto' : 'Título'; ?></th>
                <th>Estado</th> <!-- status -->
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- Traducir--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['bannered'] as $banner) :
                $banner_title = (!empty($banner->name)) ? $banner->name : $banner->title;
                ?>
            <tr>
                <td><?php echo ($banner->active) ? '<strong>'.$banner_title.'</strong>' : $banner_title; ?></td>
                <td><?php echo $banner->status; ?></td>
                <td><?php echo $banner->order; ?></td>
                <td><a href="/admin/banners/up/<?php echo $banner->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/banners/down/<?php echo $banner->id; ?>">[&darr;]</a></td>
                <td><a href="/admin/banners/edit/<?php echo $banner->id; ?>">[Editar]</a></td>
                <td><?php if ($banner->active) : ?>
                <a href="/admin/banners/active/<?php echo $banner->id; ?>/off">[Ocultar]</a>
                <?php else : ?>
                <a href="/admin/banners/active/<?php echo $banner->id; ?>/on">[Mostrar]</a>
                <?php endif; ?></td>
                <td>
                <?php if ($transNode || $translator) : ?>
                <a href="/translate/node/<?php echo $node ?>/banner/edit/<?php echo $banner->id; ?>" target="_blank">[Traducir]</a>
                <?php endif; ?>
                </td>
                <td><a href="/admin/banners/remove/<?php echo $banner->id; ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
