<?php
use Goteo\Library\Text;

$posts = $this['posts'];

if (count($posts) > 1) : ?>
<script type="text/javascript">
    $(function(){
        $('#node-news').slides({
            container: 'slder_container',
            paginationClass: 'slderpag',
            generatePagination: false,
            play: 0
        });
    });
</script>
<?php endif; ?>
<div id="node-news" class="content_widget rounded-corners">
    <h2><?php echo Text::get('node-home-posts-header'); ?>
    <span class="line"></span>
    </h2>
    
    <div class="slder_container">

        <?php if (count($posts)==1) echo '<div style="position:relative; height: 175px;">'; ?>
        <?php $i = 1; foreach ($posts as $post) : ?>
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
                <h3><?php echo $post->title; ?></h3>
                <?php if (!empty($post->author)) : ?><div class="author"><a href="/blog/?author=<?php echo $post->author ?>"><?php echo Text::get('regular-by') ?> <?php echo $post->user->name ?></a></div><?php endif; ?>
                <div class="description"><?php echo Text::recorta($post->text, 350) ?><br /></div>

                <div class="read_more"><a href="/blog/<?php echo $post->id; ?>"><?php echo Text::get('regular-read_more') ?></a></div>
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
