<?php

use Goteo\Library\Text,
    Goteo\Model;

// sacar los posts publicados y el actual
$posts = Model\Blog\Post::getAll(1);

?>
<div class="widget board">
    <form method="post" action="/admin/footer">

        <input type="hidden" name="action" value="<?php echo $vars['action']; ?>" />
        <input type="hidden" name="order" value="<?php echo $vars['post']->order; ?>" />
        <input type="hidden" name="footer" value="1" />

        <p>
            <label for="home-post">Entrada:</label><br />
            <select id="home-post" name="post">
                <option value="" >Seleccionar la entrada a publicar en el footer</option>
            <?php foreach ($posts as $post) : ?>
                <option value="<?php echo $post->id; ?>"<?php if ($vars['post']->post == $post->id) echo' selected="selected"';?>><?php echo $post->title . ' ['. $post->date . ']'; ?></option>
            <?php endforeach; ?>
            </select>
        </p>

        <p>Solo se está asignando al footer una entrada ya publicada. Para gestionar las entradas ir a la <a href="/admin/blog" target="_blank">gestión de blog</a></p>

        <input type="submit" name="save" value="Guardar" />
    </form>
</div>
