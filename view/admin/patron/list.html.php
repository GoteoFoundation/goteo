<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ACL::check('/translate') ? true : false;
?>
<a href="/admin/patron/add" class="button red">Nuevo apadrinamiento</a>

<div class="widget board">
    <?php if (!empty($this['patroned'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Proyecto</th> <!-- preview -->
                <th>Padrino</th> <!-- user -->
                <th>Título</th> <!-- title -->
                <th>Estado</th> <!-- status -->
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- On/Off --></th>
                <th><!-- Traducir--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['patroned'] as $promo) : ?>
            <tr>
                <td><a href="/project/<?php echo $promo->project; ?>" target="_blank" title="Preview"><?php echo substr($promo->name, 0, 40); ?></a></td>
                <td><?php echo $promo->user->name; ?></td>
                <td><?php echo ($promo->active) ? '<strong>'.$promo->title.'</strong>' : $promo->title; ?></td>
                <td><?php echo $promo->status; ?></td>
                <td><?php echo $promo->order; ?></td>
                <td><a href="/admin/patron/up/<?php echo $promo->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/patron/down/<?php echo $promo->id; ?>">[&darr;]</a></td>
                <td><a href="/admin/patron/edit/<?php echo $promo->id; ?>">[Editar]</a></td>
                <td><?php if ($promo->active) : ?>
                <a href="/admin/patron/active/<?php echo $promo->id; ?>/off">[Ocultar]</a>
                <?php else : ?>
                <a href="/admin/patron/active/<?php echo $promo->id; ?>/on">[Mostrar]</a>
                <?php endif; ?></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/patron/edit/<?php echo $promo->id; ?>" >[Traducir]</a></td>
                <?php endif; ?>
                <td><a href="/admin/patron/remove/<?php echo $promo->id; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>