<?php
	use Goteo\Library\Text,
			Goteo\Model\Blog\Post;
			$post = Post::get($this['post']);
			$level = (int) $this['level'] ?: 3;
			//@TODO: Si el usuario es el dueño del blog o tiene permiso para moderar, boton de borrar comentario
?>
<?php if (!empty($post->comments)): ?>
	<h<?php echo $level + 2?>><?php echo Text::get('blog-coments-header'); ?></h<?php echo $level + 2?>>
		<?php foreach ($post->comments as $comment) : ?>
			<div class="message">
				<div class="arrow-up"></div>
			   <div class="avatar"><img src="/image/<?php echo $comment->user->avatar->id; ?>/50/50" alt="" /></div>
			   <h<?php echo $level+3 ?> class="user"><?php echo htmlspecialchars($comment->user->name) ?></h<?php echo $level+3 ?>>
			   <div class="date"><span><?php echo $comment->date ?></span></div>
			   <blockquote><?php echo $comment->text; ?></blockquote>
		   </div>
		<?php endforeach; ?>
		<?php else : ?>
			<p><?php echo Text::get('blog-comments_no_comments'); ?></p>
		<?php endif; ?>
