<?php

use Goteo\Library\Text,
    Goteo\Model\Blog\Post,
    Goteo\Core\View;

$post = $this['post'];
?>
<div id="post">
    <?php  if (!empty($post->image)) : ?>
        <div class="image">
            <img src="<?php echo $post->image->getLink(228, 130); ?>" class="image_mobile" alt="Imagen"/>
        </div>
    <?php endif; ?>
    <div class="content">
        <h3><a href="<?php echo '/blog/'.$post->id; ?>" target="_blank"><?php echo $post->title; ?></a></h3>
        <?php if (!empty($post->author)) : ?><div class="author"><a href="/user/profile/<?php echo $post->author ?>"><?php echo Text::get('regular-by') ?> <?php echo $post->user_name ?></a></div><?php endif; ?>

        <div class="description"><?php echo Text::recorta($post->text, 150); ?><br /></div>

        <div class="read_more"><a href="<?php echo '/blog/'.$post->id; ?>" target="_blank"><?php echo Text::get('regular-read_more') ?></a></div>
    </div>
</div>
