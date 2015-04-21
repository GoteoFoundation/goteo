<?php
use Goteo\Core\View;

$news = $this->news;

?>

<div class="clip">
    <img src="<?php echo SRC_URL; ?>/view/css/home/clip.png" width="70"/>
</div>
<div class="clip_sup">
</div>
<div class="news">
    <h4 class="title"><?=$this->text('home-news-header')?></h4>

    <div id="slides_news" class="newrow">
        <?php if (count($news) > 4) : ?>
            <div class="arrow-left">
                <a class="prev">prev</a>
            </div>
        <?php endif ?>
        <div class="slder_news">
        <?php
        array_walk($this->chunk($news, 4, '&nbsp;'), function($row) {
            echo '<div class="row">';
            foreach($row as $new) {
                echo '<div class="new"><a href="' . $new->url .'" class="tipsy" title="' . $this->e($new->title) . '" target="_blank" rel="nofollow"><img src="' . $new->image->getLink(150, 85)  . '" alt="' . $this->e($new->title) . '" /></a></div>';
            }
            echo "</div>\n";
        }); ?>
        </div>
        <?php if (count($news) > 4) : ?>
        <div class="arrow-right">
            <a class="next">next</a>
        </div>
        <?php endif ?>
    </div>

</div>

