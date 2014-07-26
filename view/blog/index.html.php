<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image,
    Goteo\Model\Blog\Post;

$URL = \SITE_URL;

$blog = $this['blog'];
$posts = $blog->posts;
$filters = $this['filters'];
$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters = "&{$key}={$value}";
}
if (!empty($this['post'])) {
    $post = Post::get($this['post'], LANG);
}
$bodyClass = 'blog';

// metas og: para que al compartir en facebook coja bien el nombre y las imagenes del blog
if ($this['show'] == 'list') {
    $ogmeta = array(
        'title' => 'Goteo Blog',
        'description' => Text::get('regular-by').' Goteo',
        'url' => SITE_URL . '/blog'
    );

    foreach ($posts as $post) {
        if (count($post->gallery) > 1) {
            foreach ($post->gallery as $pbimg) {
                if ($pbimg instanceof Image) {
                    $ogmeta['image'][] = $pbimg->getLink(500, 285);
                }
            }
        } elseif (!empty($post->image)) {
            $ogmeta['image'][] = $post->image->getLink(500, 285);
        }
    }
} elseif ($this['show'] == 'post') {
    $ogmeta = array(
        'title' => $post->title,
        'description' => Text::get('regular-by').' '.$post->user->name,
        'url' => SITE_URL . '/blog/'.$post->id
    );

    if (count($post->gallery) > 1) {
        foreach ($post->gallery as $pbimg) {
            if ($pbimg instanceof Image) {
                $ogmeta['image'][] = $pbimg->getLink(500, 285);
            }
        }
    } elseif (!empty($post->image)) {
        $ogmeta['image'] = $post->image->getLink(500, 285);
    }
}


// paginacion
require_once 'library/pagination/pagination.php';

$pagedResults = new \Paginated($posts, 7, isset($_GET['page']) ? $_GET['page'] : 1);

include 'view/prologue.html.php';
include 'view/header.html.php'; 
?>
	<div id="sub-header-secondary">
		<div class="clearfix">
			<h2><a href="/blog">GOTEO<span class="red">BLOG</span></a></h2>
            <?php echo new View('view/header/share.html.php') ?>
		</div>
	</div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

	<div id="main" class="threecols">
		<div id="blog-content">
			<?php if ($this['show'] == 'list') : ?>
				<?php if (!empty($posts)) : ?>
					<?php while ($post = $pagedResults->fetchPagedRow()) : ?>
						<div class="widget blog-content-module">
							<?php echo new View('view/blog/post.html.php', array('post' => $post->id, 'show' => 'list')); ?>
							<?php echo new View('view/blog/share.html.php', array('urls' => Text::shareLinks($URL . '/blog/' . $post->id, $post->title))); ?>
                            <div class="comments-num"><a href="/blog/<?php echo $post->id; ?>"><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments'); ?></a></div>
						</div>
					<?php endwhile; ?>
                    <ul id="pagination">
                        <?php   $pagedResults->setLayout(new DoubleBarLayout());
                                echo $pagedResults->fetchPagedNavigation($the_filters); ?>
                    </ul>
				<?php else : ?>
					<p>No hay entradas</p>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this['show'] == 'post') : ?>
				<div class="widget post">
					<?php echo new View('view/blog/post.html.php', $this); ?>
                    <?php echo new View('view/blog/share.html.php', array('urls' => Text::shareLinks($URL . '/blog/' . $post->id, $post->title))); ?>
					<div class="comments-num"><a href="/blog/<?php echo $post->id; ?>"><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments'); ?></a></div>
				</div>
                <?php echo new View('view/blog/comments.html.php', $this) ?>
                <?php echo new View('view/blog/sendComment.html.php', $this) ?>
			<?php endif; ?>
		</div>
		<div id="blog-sidebar">
			<?php echo new View('view/blog/side.html.php', array('blog'=>$this['blog'], 'type'=>'posts')) ; ?>
			<?php echo new View('view/blog/side.html.php', array('blog'=>$this['blog'], 'type'=>'tags')) ; ?>
			<?php echo new View('view/blog/side.html.php', array('blog'=>$this['blog'], 'type'=>'feed')) ; ?>
		</div>

	</div>
<?php
    include 'view/footer.html.php';
	include 'view/epilogue.html.php';
