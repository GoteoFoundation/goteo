<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$news = $this['news'];

if (count($news) > 4) :  ?>
<script type="text/javascript">
    $(function(){
        $('#slides_news').slides({
            container: 'slder_news',
            generatePagination: false,
            play: 0
        });
    });
</script>
<?php endif; ?>
<div class="news">

    <h2 class="title"><?php echo Text::get('home-news-header'); ?></h2>

    <div id="slides_news" class="newrow">
        <?php if (count($news) > 4) : ?>
            <div class="arrow-left">
                <a class="prev">prev</a>
            </div>
        <?php endif ?>
        <div class="slder_news">
            <div class="row">
            <?php $c=1; foreach ($news as $new) { ?>
                <div class="new">
                    <a href="<?php echo $new->url ?>" title="<?php echo $new->title ?>" target="_blank" rel="nofollow"><img src="<?php echo $new->image->getLink(150, 85) ?>" alt="<?php echo $new->title ?>" /></a>
                </div>
            <?php
                if ( ($c % 4) == 0 && $c != count($news)) { echo '</div><div class="row">'; }
            $c++; } ?>
            </div>
        </div>
        <?php if (count($news) > 4) : ?>
        <div class="arrow-right">
            <a class="next">next</a>
        </div>
        <?php endif ?>
    </div>

</div>