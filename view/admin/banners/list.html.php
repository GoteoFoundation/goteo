<?php

use Goteo\Library\Text;

?>
<a href="/admin/banner/add" class="button red">Nuevo banner</a>

<div class="widget board">
    <table>
        <thead>
            <tr>
                <th>Proyecto</th> <!-- preview -->
                <th>Título</th> <!-- title -->
                <th>Estado</th> <!-- status -->
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['bannerd'] as $banner) : ?>
            <tr>
                <td><a href="/project/<?php echo $banner->project; ?>" target="_blank" title="Preview"><?php echo $banner->name; ?></a></td>
                <td><?php echo $banner->title; ?></td>
                <td><?php echo $banner->status; ?></td>
                <td><?php echo $banner->order; ?></td>
                <td><a href="/admin/banner/up/<?php echo $banner->project; ?>">[&uarr;]</a></td>
                <td><a href="/admin/banner/down/<?php echo $banner->project; ?>">[&darr;]</a></td>
                <td><a href="/admin/banner/edit/<?php echo $banner->project; ?>">[Editar]</a></td>
                <td><a href="/admin/banner/remove/<?php echo $banner->project; ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>