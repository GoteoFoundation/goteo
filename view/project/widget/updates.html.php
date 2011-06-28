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
        $posts = $blog->posts;
        $action = 'list';
        $this['show'] = 'list';
    } else {
        $post = Post::get($post);
        $action = 'post';
        $this['show'] = 'post';
    }
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
    </div>
    <?php echo new View('view/blog/comments.html.php', array('post' => $post->id)); ?>
    <?php echo new View('view/blog/sendComment.html.php', array('post' => $post->id, 'project' => $project->id)); ?>
    <?php endif ?>
    <!-- Lista de entradas -->
    <?php if ($action == 'list') : ?>
        <?php if (!empty($posts)) : ?>
            <?php foreach ($posts as $post) :
                
                    $share_title = $post->title;
                    $share_url = $this['show'] == SITE_URL . '/project/'.$project->id.'/updates/' . $post->id;
                    $facebook_url = 'http://facebook.com/sharer.php?u=' . rawurlencode($share_url) . '&t=' . rawurlencode($share_title . ' | Goteo.org');
                    $twitter_url = 'http://twitter.com/home?status=' . rawurlencode($share_title . ': ' . $share_url . ' #Goteo');
                ?>
                <div class="widget post">
                    <?php echo new View('view/blog/post.html.php', array('post' => $post->id, 'show' => 'list', 'url' => '/project/'.$project->id.'/updates/')); ?>
					<ul class="share-goteo">
						<li class="sharetext"><?php echo Text::get('regular-share_this'); ?></li>
						<li class="twitter"><a href="<?php echo htmlspecialchars($twitter_url) ?>" onclick="alert('desactivado hasta puesta en marcha'); return false;"><?php echo Text::get('regular-twitter'); ?></a></li>
						<li class="facebook"><a href="<?php echo htmlspecialchars($facebook_url) ?>" onclick="alert('desactivado hasta puesta en marcha'); return false;"><?php echo Text::get('regular-share-facebook'); ?></a></li>
					</ul>
					<div class="comments-num"><a href="/project/<?php echo $project->id; ?>/updates/<?php echo $post->id; ?>"><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments'); ?></a></div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p><?php echo Text::get('blog-no_posts'); ?></p>
        <?php endif; ?>
    <?php endif; ?>
    
</div>