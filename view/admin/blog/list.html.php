<?php

use Goteo\Library\Text;

?>
<a href="/admin/blog/add" class="button red">Nueva entrada</a>

<div class="widget board">
    <table>
        <thead>
            <tr>
                <th>Título</th> <!-- title -->
                <th>Fecha</th> <!-- date -->
                <th>Publicado</th>
                <th>En portada</th>
                <th>Al pie</th>
                <td><!-- Edit --></td>
                <td></td><!-- preview -->
                <td><!-- Remove --></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['posts'] as $post) : ?>
            <tr>
                <td><?php echo $post->title; ?></td>
                <td><?php echo $post->date; ?></td>
                <td><?php echo $post->publish ? 'Sí' : ''; ?></td>
                <td><?php echo $post->home ? 'Sí' : ''; ?></td>
                <td><?php echo $post->footer ? 'Sí' : ''; ?></td>
                <td><a href="/admin/blog/edit/<?php echo $post->id; ?>">[Editar]</a></td>
                <td><a href="/blog/<?php echo $post->id; ?>">[Ver publicado]</a></td>
                <td><a href="/admin/blog/remove/<?php echo $post->id; ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>