<?php

use Goteo\Library\Text;

?>

<a href="/admin/footer/add" class="button">Nueva entrada al pie</a>

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
                <td><a href="/admin/footer/up/<?php echo $post->id ?>/footer">[&uarr;]</a></td>
                <td><a href="/admin/footer/down/<?php echo $post->id ?>/footer">[&darr;]</a></td>
                <td><a href="/admin/footer/remove/<?php echo $post->id ?>/footer">[Quitar del footer]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
