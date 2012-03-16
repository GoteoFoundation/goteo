<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$status = $this['status'];

?>
<a href="/admin/campaign/add" class="button red">Nueva campaña destacada</a>

<div class="widget board">
    <?php if (!empty($this['setted'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Campaña</th> <!-- preview -->
                <th>Estado</th> <!-- status -->
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- On/Off --></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['setted'] as $campa) : ?>
            <tr>
                <td><a href="/call/<?php echo $campa->project; ?>" target="_blank" title="Preview"><?php echo $campa->name; ?></a></td>
                <td><?php echo ($campa->active) ? '<strong>'.$campa->title.'</strong>' : $campa->title; ?></td>
                <td><?php echo $status[$campa->status]; ?></td>
                <td><?php echo $campa->order; ?></td>
                <td><a href="/admin/campaings/up/<?php echo $campa->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/campaings/down/<?php echo $campa->id; ?>">[&darr;]</a></td>
                <td><a href="/admin/campaings/edit/<?php echo $campa->id; ?>">[Editar]</a></td>
                <td><?php if ($campa->active) : ?>
                <a href="/admin/campaings/active/<?php echo $campa->id; ?>/off">[Ocultar]</a>
                <?php else : ?>
                <a href="/admin/campaings/active/<?php echo $campa->id; ?>/on">[Mostrar]</a>
                <?php endif; ?></td>
                <td><a href="/admin/campaings/remove/<?php echo $campa->id; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>