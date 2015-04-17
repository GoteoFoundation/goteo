<?php
use Goteo\Model;

$node = $vars['node'];

$blog  = Model\Blog::get($node, 'node');
$posts = Model\Blog\Post::getAll($blog->id, null, false);
?>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Título</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post) : ?>
            <tr>
                <td><a href="/translate/node/<?php echo $node ?>/post/edit/<?php echo $post->id; ?>">[Translate]</a></td>
                <td><?php echo $post->title; ?></td>
                <td><?php echo $post->date; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
