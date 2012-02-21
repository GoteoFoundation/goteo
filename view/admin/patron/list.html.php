<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

?>
<a href="/admin/patron/add" class="button red">Nuevo apadrinamiento</a>

<div class="widget board">
    <?php if (!empty($this['patroned'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Proyecto</th> <!-- preview -->
                <th>Padrino</th> <!-- user -->
                <th>Estado</th> <!-- status -->
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['patroned'] as $promo) : ?>
            <tr>
                <td><a href="/project/<?php echo $promo->project; ?>" target="_blank" title="Preview"><?php echo $promo->name; ?></a></td>
                <td><?php echo $promo->user->name; ?></td>
                <td><?php echo $promo->status; ?></td>
                <td><?php echo $promo->order; ?></td>
                <td><a href="/admin/patron/up/<?php echo $promo->project; ?>">[&uarr;]</a></td>
                <td><a href="/admin/patron/down/<?php echo $promo->project; ?>">[&darr;]</a></td>
                <td><a href="/admin/patron/edit/<?php echo $promo->project; ?>">[Editar]</a></td>
                <td><a href="/admin/patron/remove/<?php echo $promo->project; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>