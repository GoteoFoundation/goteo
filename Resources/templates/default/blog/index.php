<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image,
    Goteo\Model\Blog\Post,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$URL = \SITE_URL;

$blog = $this->blog;
$posts = $blog->posts;
$filters = $this->filters;
$the_filters = '';
foreach ($filters as $key=>$value) {
    $the_filters = "&{$key}={$value}";
}
if (!empty($this->vars['post'])) {
    $post = Post::get($this->vars['post'], LANG);
}

$bodyClass = 'blog';

// metas og: para que al compartir en facebook coja bien el nombre y las imagenes del blog
if ($this->show == 'list') {
    // SEO metas
    $title=$this->text('meta-title-blog');
    $description=$this->text('meta-description-blog');
    $ogmeta = array(
        'title' => 'Goteo Blog',
        'description' => Text::get('regular-by').' Goteo',
        'url' => SITE_URL . '/blog'
    );

    foreach ($posts as $post) {
        if (count($post->gallery) > 1) {
            foreach ($post->gallery as $pbimg) {
                if ($pbimg instanceof Image) {
                    $ogmeta['image'][] = $pbimg->getLink(500, 285, false, true);
                }
            }
        } elseif ((!empty($post->image))&&($post->image instanceof Image)) {
            $ogmeta['image'][] = $post->image->getLink(500, 285, false, true);
        }
    }
} elseif ($this->show == 'post') {

    $title=$post->title;
    $description=htmlspecialchars(strip_tags($this->text_truncate($post->text, 155)), ENT_QUOTES);

    $ogmeta = array(
        'title' => htmlspecialchars($post->title, ENT_QUOTES),
        'description' => Text::get('regular-by').' '.$post->user->name,
        'url' => SITE_URL . '/blog/'.$post->id
    );

    if (count($post->gallery) > 1) {
        foreach ($post->gallery as $pbimg) {
            if ($pbimg instanceof Image) {
                $ogmeta['image'][] = $pbimg->getLink(500, 285, false, true);
            }
        }
    } elseif ((!empty($post->image))&&($post->image instanceof Image)) {
        $ogmeta['image'] = $post->image->getLink(500, 285, false, true);
    }
}


$pagedResults = new Paginated($posts, 7, isset($_GET['page']) ? $_GET['page'] : 1);

$this->layout("layout", [
    'bodyClass' => 'blog',
    'title' => $title,
    'meta_description' => $description
    ]);

$this->section('content');

?>
	<div id="sub-header-secondary">
		<div class="clearfix">
			<h2><a href="/blog">GOTEO<span class="red">BLOG</span></a></h2>
            <?php echo View::get('header/share.html.php') ?>
		</div>
	</div>

	<div id="main" class="threecols">
		<div id="blog-content">
			<?php if ($this->show == 'list') : ?>
				<?php if (!empty($posts)) : ?>
					<?php while ($post = $pagedResults->fetchPagedRow()) : ?>
						<div class="widget blog-content-module">
							<?php echo View::get('blog/post.html.php', array('post' => $post->id, 'show' => 'list')); ?>
							<?php echo View::get('blog/share.html.php', array('urls' => Text::shareLinks($URL . '/blog/' . $post->id, $post->title))); ?>
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
			<?php if ($this->show == 'post') : ?>
				<div class="widget post">
					<?php echo View::get('blog/post.html.php', $this->vars) ?>
                    <?php echo View::get('blog/share.html.php', array('urls' => Text::shareLinks($URL . '/blog/' . $post->id, $post->title))); ?>
					<div class="comments-num"><a href="/blog/<?php echo $post->id; ?>"><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments'); ?></a></div>
				</div>
                <?php echo View::get('blog/comments.html.php', $this->vars) ?>
                <?php echo View::get('blog/sendComment.html.php', $this->vars) ?>
			<?php endif; ?>
		</div>
		<div id="blog-sidebar">
			<?php echo View::get('blog/side.html.php', array('blog'=>$this->blog, 'type'=>'posts')) ; ?>
			<?php echo View::get('blog/side.html.php', array('blog'=>$this->blog, 'type'=>'tags')) ; ?>
			<?php echo View::get('blog/side.html.php', array('blog'=>$this->blog, 'type'=>'feed')) ; ?>
		</div>

	</div>

<?php $this->replace() ?>
