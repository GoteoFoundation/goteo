<?php
use Goteo\Library\Text;

?>
<a href="/admin/patron" class="button">Volver</a>

<div class="widget board">
    <?php if (!empty($vars['patrons'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Título</th> <!-- title -->
                <th>Posición</th> <!-- order -->
                <td><!-- Move up --></td>
                <td><!-- Move down --></td>
                <td><!-- Remove --></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['patrons'] as $user) : ?>
            <tr>
                <td><?php echo $user->name; ?></td>
                <td><?php echo $user->order; ?></td>
                <td><a href="/admin/patron/up/<?php echo $user->id ?>">[&uarr;]</a></td>
                <td><a href="/admin/patron/down/<?php echo $user->id ?>">[&darr;]</a></td>
                <td><a href="/admin/patron/remove_home/<?php echo $user->id ?>">[Quitar de la Portada]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
