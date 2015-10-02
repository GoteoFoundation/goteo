<?php

// $posts = $this->raw('posts');
$posts = $this->posts;

?>

<div id="learn" class="widget learn">
    <h2 class="title"><?php echo $this->text('home-posts-header'); ?></h2>
    <div class="slder_container" style="max-height:290px; overflow:hidden; <?php if (count($posts)==1) echo ' display:block;'; ?>">

        <?php $i = 1; foreach ($posts as $post) : ?>
        <div class="slder_slide">
            <div class="post" id="home-post-<?php echo $i; ?>" style="display:block;">
                <?php  if (!empty($post->media->url)) : ?>
                    <div class="embed">
                        <?php echo $post->media->getEmbedCode(); ?>
                    </div>
                <?php elseif (!empty($post->image)) : ?>
                    <div class="image">
                        <img src="<?php echo $post->image->getLink(500, 285); ?>" alt="Imagen"/>
                    </div>
                <?php endif; ?>
                <h3><?php if ($post->owner_type == 'project') { echo '<a href="/project/'.$post->owner_id.'/updates/'.$post->id.'">'.$this->text('project-menu-home').' '.$post->owner_name.'</a>: ' . $post->title; }
                else { echo '<a href="/blog/'.$post->id.'">'.$post->title.'</a>'; } ?></h3>
                <?php if (!empty($post->author)) : ?>
                    <div class="author"><a href="/user/profile/<?php echo $post->author ?>"><?php echo $this->text('regular-by') ?> <?php echo $post->user->name ?></a></div>
                <?php endif; ?>
                <div class="post_text"><?php if ($post->id == 728) echo $this->text_truncate($post->text, 400); else echo $this->text_truncate($post->text, 600); ?></div>

                <div class="read_more"><a href="<?php echo ($post->owner_type == 'project') ? '/project/'.$post->owner_id.'/updates/'.$post->id : '/blog/'.$post->id; ?>"><?php echo $this->text('regular-read_more') ?></a></div>
            </div>
        </div>
        <?php $i++; endforeach; ?>
    </div>
    <a class="prev">prev</a>
    <ul class="slderpag">
        <?php $i = 1; foreach ($posts as $post) : ?>
        <li><a href="#" id="navi-home-post-<?php echo $i ?>" rel="home-post-<?php echo $i ?>" class="tipsy navi-home-post" title="<?php echo $post->title ?>">
            <?php echo $post->title ?></a>
        </li>
        <?php $i++; endforeach ?>
    </ul>
    <a class="next">next</a>

</div>

