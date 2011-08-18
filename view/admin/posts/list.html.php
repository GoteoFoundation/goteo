<?php

use Goteo\Library\Text;

?>
<a href="/admin/posts/add/home" class="button red">Nueva entrada en portada</a>

<div class="widget board">
    <h3 class="title">Portada</h3>
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
            <?php foreach ($this['posts'] as $post) : ?>
            <tr>
                <td><?php echo $post->title; ?></td>
                <td><?php echo $post->order; ?></td>
                <td><a href="/admin/posts/up/<?php echo $post->id ?>/home">[&uarr;]</a></td>
                <td><a href="/admin/posts/down/<?php echo $post->id ?>/home">[&darr;]</a></td>
                <td><a href="/admin/posts/remove/<?php echo $post->id ?>/home">[Quitar de la portada]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>