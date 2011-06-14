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
?>
<h<?php echo $level + 1?>><?php echo $post->title; ?></h<?php echo $level + 1?>>
<span style="display:block;"><?php echo $post->date; ?></span>
<blockquote><?php echo $post->text; ?></blockquote>
<?php if (!empty($post->image)) : ?>
    <img src="/image/<?php echo $post->image->id; ?>/110/110" alt="Imagen"/>
<?php endif; ?>
<?php if (!empty($post->media)) : ?>
    <?php echo $post->media->getEmbedCode(); ?>
<?php endif; ?>