<?php
use Goteo\Model\Blog;

$node = $vars['node'];

$blog = Blog::get($node->id, 'node');
?>
<div class="widget board">
    <?php if (!empty($blog->posts)) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- translate --></th>
                <th colspan="3">Título</th> <!-- title -->
                <th>Fecha</th> <!-- date -->
                <th>Autor</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($blog->posts as $post) : ?>
            <tr>
                <td><a href="/translate/post/edit/<?php echo $post->id; ?>">[Translate]</a></td>
                <td><?php echo $post->title; ?></td>
                <td><?php echo $post->date; ?></td>
                <td><?php echo $post->author->name; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
