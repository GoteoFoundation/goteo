<?php

$posts = $this->posts;

if (count($posts) > 1) : ?>
<script type="text/javascript">
    $(function(){
        $('#channel-news').slides({
            container: 'slder_container',
            paginationClass: 'slderpag',
            generatePagination: false,
            play: 0
        });
    });
</script>
<?php endif; ?>
<div id="channel-news" class="content_widget rounded-corners">
    <h2><?= $this->text('node-home-posts-header'); ?>
    <span class="line"></span>
    </h2>

    <div class="slder_container">

        <?php if (count($posts)==1) echo '<div style="position:relative; height: 175px;">'; ?>
        <?php $i = 1; foreach ($posts as $post) :
            if ($post->id == 728) $post->title = $this->text_recorta($post->title, 150);
            ?>
        <div class="slder_slide">
            <div class="post" id="home-post-<?php echo $i; ?>" style="display:block;">
                <?php  if (!empty($post->media->url)) : ?>
                    <div class="embed">
                        <?php echo $post->media->getEmbedCode(); ?>
                    </div>
                <?php elseif (!empty($post->image)) : ?>
                    <div class="image">
                        <img src="<?php echo $post->image->getLink(330, 175); ?>" alt="Imagen"/>
                    </div>
                <?php endif; ?>
                <h3><?php if ($post->owner_type == 'project') { echo '<a href="/project/'.$post->owner_id.'">'.$this->text('project-menu-home').' '.$post->owner_name.'</a>: '; } echo $post->title; ?></h3>
                <?php if (!empty($post->author)) : ?><div class="author"><a href="/user/profile/<?php echo $post->author ?>"><?= $this->text('regular-by') ?> <?php echo $post->user->name ?></a></div><?php endif; ?>
                <div class="description"><?php if ($post->id == 728) echo $this->text_recorta($post->text, 200); else echo $this->text_recorta($post->text, 350); ?><br /></div>

                <div class="read_more"><a href="<?php echo ($post->owner_type == 'project') ? '/project/'.$post->owner_id.'/updates/'.$post->id : '/blog/'.$post->id; ?>"><?php echo $this->text_recorta('regular-read_more') ?></a></div>
            </div>
        </div>
        <?php $i++; endforeach; ?>
        <?php if (count($posts)==1) echo '</div>'; ?>
    </div>
<?php if (count($posts) > 1) : ?>
    <a class="prev">prev</a>
    <ul class="slderpag">
        <?php $i = 1; foreach ($posts as $post) : ?>
        <li><a href="#" id="navi-home-post-<?php echo $i ?>" rel="home-post-<?php echo $i ?>" class="tipsy navi-home-post" title="<?php echo htmlspecialchars($post->title) ?>">
            <?php echo htmlspecialchars($post->title) ?></a>
        </li>
        <?php $i++; endforeach ?>
    </ul>
    <a class="next">next</a>
<?php endif; ?>

</div>
