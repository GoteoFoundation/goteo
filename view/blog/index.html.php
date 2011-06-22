<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$blog = $this['blog'];
$posts = $blog->posts;
$tag = $this['tag'];
$bodyClass = 'blog';

// paginacion
require_once 'library/pagination/pagination.php';

$pagedResults = new \Paginated($posts, 7, isset($_GET['page']) ? $_GET['page'] : 1);

include 'view/prologue.html.php';
include 'view/header.html.php'; 
?>
	<div id="sub-header-secondary">
		<div class="clearfix">
			<h2>GOTEO<span class="red">BLOG</span></h2>
			<ul class="share-goteo">
				<li class="twitter"><a href="#"><?php echo Text::get('regular-share-twitter'); ?></a></li>
				<li class="facebook"><a href="#"><?php echo Text::get('regular-share-facebook'); ?></a></li>
				<li class="rss"><a href="#"><?php echo Text::get('regular-share-rss'); ?></a></li>
			</ul>
		</div>
	</div>
	<div id="main" class="threecols">
		<div id="blog-content">
			<?php if ($this['show'] == 'list') : ?>
				<?php if (!empty($posts)) : ?>
					<?php while ($post = $pagedResults->fetchPagedRow()) : ?>
						<div class="widget blog-content-module">
							<?php echo new View('view/blog/post.html.php', array('post'=>$post->id)); ?>
							<?php if ($this['show'] == 'list') : ?>
								<ul class="share-goteo">
									<li class="sharetext">Compartir en:</li>
									<li class="twitter"><a href="#">Twitter</a></li>
									<li class="facebook"><a href="#">Facebook</a></li>
								</ul>
							<?php endif; ?>
							<?php if ($this['show'] == 'list') : ?>
								<div class="comments-num"><a href="/blog/<?php echo $post->id; ?>"><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments'); ?></a></div>
							<?php else : ?>
								<div class="comments-num"><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments'); ?></div>
							<?php endif; ?>
						</div>
					<?php endwhile; ?>
                    <div class="pagination">
                        <?php   $pagedResults->setLayout(new DoubleBarLayout());
                                echo $pagedResults->fetchPagedNavigation(); ?>
                    </div>
				<?php else : ?>
					<p>No hay entradas</p>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this['show'] == 'post') : ?>
				<div class="widget post">
					<?php echo new View('view/blog/post.html.php', $this); ?>
					<ul class="share-goteo">
							<li class="sharetext">Compartir en:</li>
							<li class="twitter"><a href="#">Twitter</a></li>
							<li class="facebook"><a href="#">Facebook</a></li>
					</ul>
					<div class="comments-num"><a href="/blog/<?php echo $post->id; ?>"><?php echo $post->num_comments > 0 ? $post->num_comments . ' ' .Text::get('blog-comments') : Text::get('blog-no_comments'); ?></a></div>
				</div>
				<div>
					<?php
						echo new View('view/blog/comments.html.php', $this);
						echo new View('view/blog/sendComment.html.php', $this);
					?>
				</div>
			<?php endif; ?>
		</div>
		<div id="blog-sidebar">
			<?php echo new View('view/blog/side.html.php', array('blog'=>$this['blog'], 'type'=>'posts')) ; ?>
			<?php echo new View('view/blog/side.html.php', array('blog'=>$this['blog'], 'type'=>'tags')) ; ?>
			<?php echo new View('view/blog/side.html.php', array('blog'=>$this['blog'], 'type'=>'comments')) ; ?>
		</div>

	</div>
<?php
    include 'view/footer.html.php';
	include 'view/epilogue.html.php';
