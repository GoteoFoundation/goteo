<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Blog\Post;

$project = $this['project'];
$blog    = $this['blog'];
if (empty($this['post'])) {
    $posts = $blog->posts;
    $action = 'list';
    $this['show'] = 'list';
} else {
    $post = $this['post'];
    if (!in_array($post, array_keys($blog->posts))) {
        throw new Goteo\Core\Redirection("/project/{$project->id}/updates", Goteo\Core\Redirection::TEMPORARY);
    }
    $post = Post::get($post);
    $action = 'post';
    $this['show'] = 'post';
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
        <?php echo new View('view/blog/post.html.php', array('post' => $post->id, 'show' => 'post')); ?>
        <?php echo new View('view/blog/comments.html.php', array('post' => $post->id)); ?>
        <?php echo new View('view/blog/sendComment.html.php', array('post' => $post->id, 'project' => $project->id)); ?>
    </div>
    <?php endif ?>

    <!-- Lista de entradas -->
    <?php if ($action == 'list') : ?>
        <?php if (!empty($posts)) : ?>
        <div class="posts">
            <?php foreach ($posts as $post) : ?>
                <div class="widget">
                    <?php echo new View('view/blog/post.html.php', array('post' => $post->id, 'show' => 'list')); ?>
                   <span><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments'); ?></span>
                   <div class="more"><a href="/project/<?php echo $project->id; ?>/updates/<?php echo $post->id; ?>"><?php echo Text::get('blog-read_more'); ?></a></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else : ?>
            <p><?php echo Text::get('blog-no_posts'); ?></p>
        <?php endif; ?>
    <?php endif; ?>
    
</div>