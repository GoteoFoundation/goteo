<?php

use Goteo\Library\Text;

$node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;
?>
<a href="/admin/banners/add" class="button">Nuevo banner</a>

<div class="widget board">
    <?php if (!empty($this['bannered'])) : ?>
    <table>
        <thead>
            <tr>
                <th><?php echo ($node == \GOTEO_NODE) ? 'Proyecto' : 'Título'; ?></th>
                <th>Estado</th> <!-- status -->
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['bannered'] as $banner) :
                $banner_title = ($node == \GOTEO_NODE) ? $banner->name : $banner->title;
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
                <td><a href="/admin/banners/remove/<?php echo $banner->id; ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>