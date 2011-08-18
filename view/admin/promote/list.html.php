<?php

use Goteo\Library\Text;

?>
<a href="/admin/promote/add" class="button red">Nuevo destacado</a>

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
            <?php foreach ($this['promoted'] as $promo) : ?>
            <tr>
                <td><a href="/project/<?php echo $promo->project; ?>" target="_blank" title="Preview"><?php echo $promo->name; ?></a></td>
                <td><?php echo $promo->title; ?></td>
                <td><?php echo $promo->status; ?></td>
                <td><?php echo $promo->order; ?></td>
                <td><a href="/admin/promote/up/<?php echo $promo->project; ?>">[&uarr;]</a></td>
                <td><a href="/admin/promote/down/<?php echo $promo->project; ?>">[&darr;]</a></td>
                <td><a href="/admin/promote/edit/<?php echo $promo->project; ?>">[Editar]</a></td>
                <td><a href="/admin/promote/remove/<?php echo $promo->project; ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>