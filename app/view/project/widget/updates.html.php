<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Blog\Post,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$URL = \SITE_URL;

$project = $vars['project'];
$blog    = $vars['blog'];

if (empty($vars['post'])) {
    $posts = $blog->posts;
    $action = 'list';
    $vars['show'] = 'list';
} else {
    $post = $vars['post'];
    if (!in_array($post, array_keys($blog->posts))) {
        $posts = $blog->posts;
        $action = 'list';
        $vars['show'] = 'list';
    } else {
        $post = Post::get($post, LANG);
        $action = 'post';
        $vars['show'] = 'post';
    }
}

if ($vars['show'] == 'list') {
    // paginacion
    $pagedResults = new Paginated($posts, 7, isset($_GET['page']) ? $_GET['page'] : 1);
}

// segun lo que tengamos que mostrar :  lista o entrada
// uso la libreria blog para sacar los datos adecuados para esta vista

$level = (int) $vars['level'] ?: 3;
?>
<div class="project-updates">
    <!-- una entrada -->
    <?php if ($action == 'post') : ?>
    <div class="post widget">
        <?php echo View::get('blog/post.html.php', array('post' => $post->id, 'show' => 'post', 'url' => '/project/'.$project->id.'/updates/')); ?>
        <?php echo View::get('blog/share.html.php', array('urls' => Text::shareLinks($URL . '/project/'.$project->id.'/updates/' . $post->id, $post->title, $project->user->twitter))); ?>
    </div>
    <?php echo View::get('blog/comments.html.php', array('post' => $post->id, 'owner' => $project->owner)); ?>
    <?php echo View::get('blog/sendComment.html.php', array('post' => $post->id, 'project' => $project->id)); ?>
    <?php endif ?>
    <!-- Lista de entradas -->
    <?php if ($action == 'list') : ?>
        <?php if (!empty($posts)) : ?>
            <?php while ($post = $pagedResults->fetchPagedRow()) : ?>
                <div class="widget post">
                    <?php echo View::get('blog/post.html.php', array('post' => $post->id, 'show' => 'list', 'url' => '/project/'.$project->id.'/updates/')); ?>
                    <?php echo View::get('blog/share.html.php', array('urls' => Text::shareLinks($URL . '/project/'.$project->id.'/updates/' . $post->id, $post->title, $project->user->twitter))); ?>
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
