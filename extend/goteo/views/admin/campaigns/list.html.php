<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

?>
<a href="/admin/campaigns/add" class="button">Destacar otra convocatoria</a>

<div class="widget board">
    <?php if (!empty($vars['setted'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th> <!-- preview -->
                <th>Convocatoria</th> <!-- name -->
                <th>Estado</th> <!-- status -->
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- On/Off --></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['setted'] as $campa) : ?>
            <tr>
                <td><a href="/call/<?php echo $campa->project; ?>" target="_blank" title="Preview">[Ver]</a></td>
                <td><?php echo ($campa->active) ? '<strong>'.$campa->name.'</strong>' : $campa->name; ?></td>
                <td><?php echo $campa->status; ?></td>
                <td><?php echo $campa->order; ?></td>
                <td><a href="/admin/campaigns/up/<?php echo $campa->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/campaigns/down/<?php echo $campa->id; ?>">[&darr;]</a></td>
                <td><?php if ($campa->active) : ?>
                <a href="/admin/campaigns/active/<?php echo $campa->id; ?>/off">[Ocultar]</a>
                <?php else : ?>
                <a href="/admin/campaigns/active/<?php echo $campa->id; ?>/on">[Mostrar]</a>
                <?php endif; ?></td>
                <td><a href="/admin/campaigns/remove/<?php echo $campa->id; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
