<?php

use Goteo\Library\Text,
    Goteo\Model\Blog\Post;

$project = $this['project'];
$blog    = $this['blog'];
if (empty($this['post'])) {
    $posts = $blog->posts;
    $action = 'list';
} else {
    $post = $this['post'];
    if (!in_array($post, array_keys($blog->posts))) {
        throw new Goteo\Core\Redirection("/project/{$project->id}/updates", Goteo\Core\Redirection::TEMPORARY);
    }
    $post = Post::get($post);
    $action = 'post';
}

// segun lo que tengamos que mostrar :  lista o entrada
// uso la libreria blog para sacar los datos adecuados para esta vista

$level = (int) $this['level'] ?: 3;

?>
<div class="widget project-summary">
    
    <h<?php echo $level ?>><?php echo htmlspecialchars($project->name) ?></h<?php echo $level ?>>

    <!-- una entrada -->
    <?php if ($action == 'post') : ?>
    <div class="post">
        <h<?php echo $level + 1?>><?php echo $post->title; ?></h<?php echo $level + 1?>>
        <span style="display:block;"><?php echo $post->date; ?></span>
        <blockquote><?php echo Text::recorta($post->text, 500); ?></blockquote>
        <?php if (!empty($post->image)) : ?>
            <img src="/image/<?php echo $post->image->id; ?>/110/110" alt="Imagen"/>
        <?php endif; ?>
        <?php if (!empty($post->media)) : ?>
            <?php echo $post->media->getEmbedCode(); ?>
        <?php endif; ?>
        <p><?php echo $post->num_comments > 0 ? $post->num_comments : 'Sin'; ?> comentarios.</p>

        <?php if (!empty($post->comments)): ?>
            <h<?php echo $level + 2?>>Comentarios</h<?php echo $level + 2?>>
            <?php foreach ($post->comments as $comment) : ?>
            <div class="message">
               <span class="avatar"><img src="/image/<?php echo $comment->user->avatar->id; ?>/50/50" alt="" /></span>
               <h<?php echo $level+3 ?> class="user"><?php echo htmlspecialchars($comment->user->name) ?></h<?php echo $level+3 ?>>
               <div class="date"><span><?php echo $comment->date ?></span></div>
               <blockquote><?php echo $comment->text; ?></blockquote>
           </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="widget">
            <h<?php echo $level + 2?>>Escribe tu comentario</h<?php echo $level + 2?>>
            <form method="post" action="/message/post/<?php echo $project->id; ?>/<?php echo $post->id; ?>">
                <textarea name="message" cols="50" rows="5"></textarea>
                <input class="button" type="submit" value="Enviar" />
            </form>
        </div>

    </div>
    <?php endif ?>

    <!-- Lista de entradas -->
    <?php if ($action == 'list') : ?>
        <?php if (!empty($posts)) : ?>
        <div class="posts">
            <?php foreach ($posts as $post) : ?>
                <div class="widget">
                    <h<?php echo $level+1 ?> class="title"><?php echo $post->title; ?></h<?php echo $level+1 ?>
                    <span style="display:block;"><?php echo $post->date; ?></span>
                    <blockquote><?php echo Text::recorta($post->text, 500); ?></blockquote>
                    <?php if (!empty($post->image)) : ?>
                        <img src="/image/<?php echo $post->image->id; ?>/110/110" alt="Imagen"/>
                    <?php endif; ?>
                    <?php if (!empty($post->media)) : ?>
                        <?php echo $post->media->getEmbedCode(); ?>
                    <?php endif; ?>
                    <p><a href="/project/<?php echo $project->id; ?>/updates/<?php echo $post->id; ?>">Leer</a></p>
                    <p><a href="/project/<?php echo $project->id; ?>/updates/<?php echo $post->id; ?>"><?php echo $post->num_comments > 0 ? $post->num_comments : 'Sin'; ?> comentarios.</a></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else : ?>
            <p>No se ha publicado ninguna actualización</p>
        <?php endif; ?>
    <?php endif; ?>
    
</div>