<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Blog\Post;

$URL = (NODE_ID != GOTEO_NODE) ? NODE_URL : SITE_URL;

$project = $this['project'];
$blog    = $this['blog'];

if (empty($this['post'])) {
    $posts = $blog->posts;
    $action = 'list';
    $this['show'] = 'list';
} else {
    $post = $this['post'];
    if (!in_array($post, array_keys($blog->posts))) {
        $posts = $blog->posts;
        $action = 'list';
        $this['show'] = 'list';
    } else {
        $post = Post::get($post, LANG);
        $action = 'post';
        $this['show'] = 'post';
    }
}

if ($this['show'] == 'list') {
    // paginacion
    require_once 'library/pagination/pagination.php';

    $pagedResults = new \Paginated($posts, 7, isset($_GET['page']) ? $_GET['page'] : 1);
}

// segun lo que tengamos que mostrar :  lista o entrada
// uso la libreria blog para sacar los datos adecuados para esta vista

$level = (int) $this['level'] ?: 3;
?>
<div class="project-updates"> 
    <!-- una entrada -->
    <?php if ($action == 'post') : ?>
    <div class="post widget">
        <?php echo new View('view/blog/post.html.php', array('post' => $post->id, 'show' => 'post', 'url' => '/project/'.$project->id.'/updates/')); ?>
        <?php echo new View('view/blog/share.html.php', array('urls' => Text::shareLinks($URL . '/project/'.$project->id.'/updates/' . $post->id, $post->title.'project', $project->user->twitter))); ?>
    </div>
    <?php echo new View('view/blog/comments.html.php', array('post' => $post->id, 'owner' => $project->owner)); ?>
    <?php echo new View('view/blog/sendComment.html.php', array('post' => $post->id, 'project' => $project->id)); ?>
    <?php endif ?>
    <!-- Lista de entradas -->
    <?php if ($action == 'list') : ?>
        <?php if (!empty($posts)) : ?>
            <?php while ($post = $pagedResults->fetchPagedRow()) : ?>
                <div class="widget post">
                    <?php echo new View('view/blog/post.html.php', array('post' => $post->id, 'show' => 'list', 'url' => '/project/'.$project->id.'/updates/')); ?>
                    <?php echo new View('view/blog/share.html.php', array('urls' => Text::shareLinks($URL . '/project/'.$project->id.'/updates/' . $post->id, $post->title, $project->user->twitter))); ?>
					<div class="comments-num"><a href="/project/<?php echo $project->id; ?>/updates/<?php echo $post->id; ?>"><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments'); ?></a></div>
                </div>
            <?php endwhile; ?>
            <ul id="pagination">
                <?php   $pagedResults->setLayout(new DoubleBarLayout());
                        echo $pagedResults->fetchPagedNavigation(); ?>
            </ul>
        <?php else : ?>
            <p><?php echo Text::get('blog-no_posts'); ?></p>
        <?php endif; ?>
    <?php endif; ?>
    
</div>