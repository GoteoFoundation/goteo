<?php
use Goteo\Library\Text;
?>
<a href="/admin/blog" class="button">Volver</a>

<div class="widget board">
    <?php if (!empty($vars['posts'])) : ?>
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
            <?php foreach ($vars['posts'] as $post) : ?>
            <tr>
                <td><?php echo $post->title; ?></td>
                <td><?php echo $post->order; ?></td>
                <td><a href="/admin/blog/up/<?php echo $post->id ?>">[&uarr;]</a></td>
                <td><a href="/admin/blog/down/<?php echo $post->id ?>">[&darr;]</a></td>
                <td><a href="/admin/blog/remove_home/<?php echo $post->id ?>">[Quitar de la Portada]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
