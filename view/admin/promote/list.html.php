<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ACL::check('/translate') ? true : false;
?>
<a href="/admin/promote/add" class="button red">Nuevo destacado</a>

<div class="widget board">
    <?php if (!empty($this['promoted'])) : ?>
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
                <th><!-- On/Off --></th>
                <th><!-- Traducir--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['promoted'] as $promo) : ?>
            <tr>
                <td><a href="/project/<?php echo $promo->project; ?>" target="_blank" title="Preview"><?php echo $promo->name; ?></a></td>
                <td><?php echo ($promo->active) ? '<strong>'.$promo->title.'</strong>' : $promo->title; ?></td>
                <td><?php echo $promo->status; ?></td>
                <td><?php echo $promo->order; ?></td>
                <td><a href="/admin/promote/up/<?php echo $promo->project; ?>">[&uarr;]</a></td>
                <td><a href="/admin/promote/down/<?php echo $promo->project; ?>">[&darr;]</a></td>
                <td><a href="/admin/promote/edit/<?php echo $promo->project; ?>">[Editar]</a></td>
                <td><?php if ($promo->active) : ?>
                <a href="/admin/promote/active/<?php echo $promo->id; ?>/off">[Ocultar]</a>
                <?php else : ?>
                <a href="/admin/promote/active/<?php echo $promo->id; ?>/on">[Mostrar]</a>
                <?php endif; ?></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/contents/edit/promote-<?php echo $promo->id; ?>" target="_blank">[Traducir]</a></td>
                <?php endif; ?>
                <td><a href="/admin/promote/remove/<?php echo $promo->project; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>