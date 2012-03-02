<?php
use Goteo\Library\Text;

$post = $this['post'];
?>
<div>
    <span><?php echo htmlentities($post->title); ?></span>
    <div><?php echo Text::recorta($post->text, 600) ?></div>
</div>
