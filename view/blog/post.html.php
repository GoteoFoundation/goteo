<?php
// Vista para pintar una entrada de blog
// puede ser resumen en la lista o completa
	use Goteo\Library\Text,
		Goteo\Model\Blog\Post;
		$post = Post::get($this['post']);
		$level = (int) $this['level'] ?: 3;
	if ($this['show'] == 'list') {
		$post->text = Text::recorta($post->text, 200);
	}

    $url = empty($this['url']) ? '/blog/' : $this['url'];
?>
	<h<?php echo $level + 1?>><a href="<?php echo $url.$post->id; ?>"><?php echo $post->title; ?></a></h<?php echo $level + 1?>>
	<span class="date"><?php echo $post->fecha; ?></span>
	<?php if (!empty($post->tags)) : ?>
		<span class="categories"><?php echo implode(', ', $post->tags);  ?></span>
	<?php endif; ?>
	<?php if (!empty($post->image)) : ?>
		<div class="image">
			<img src="/image/<?php echo $post->image->id; ?>/580/580" alt="Imagen"/>
		</div>
	<?php endif; ?>
	<?php if (!empty($post->media->url)) : ?>
		<div class="embed">
			<?php echo $post->media->getEmbedCode(); ?>
		</div>
	<?php endif; ?>
	<blockquote><?php echo $post->text; ?></blockquote>