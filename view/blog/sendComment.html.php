<?php
	use Goteo\Library\Text,
			Goteo\Model\Blog\Post;
			$allow = Post::allowed($this['post']);
			$level = (int) $this['level'] ?: 3;
?>
<?php if ($allow == 1) : ?>
<div class="widget blog-comment">
    <h<?php echo $level ?> class="title"><?php echo Text::get('blog-send_comment-header'); ?></h<?php echo $level ?>>
    <form method="post" action="/message/post/<?php echo $this['post']; ?>/<?php echo $this['project']; ?>">
        <textarea name="message" cols="50" rows="5"></textarea>
        <input class="button" type="submit" value="<?php echo Text::get('blog-send_comment-button'); ?>" />
    </form>
</div>
<?php else : ?>
    <p><?php echo Text::get('blog-comments_no_allowed'); ?></p>
<?php endif; ?>
