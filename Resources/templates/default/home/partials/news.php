<?php

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
        $chunks = $this->chunk($news, 4, '&nbsp;');
        array_walk($chunks, function($row) {
            echo '<div class="row">';
            foreach($row as $new) {
                echo '<div class="new"><a href="' . $new->url .'" class="tipsy" title="' . $this->e($new->title) . '" target="_blank" rel="nofollow">';
                if($new->image && is_object($new->image)) {
                    echo '<img src="' . $new->image->getLink(150, 85)  . '" alt="' . $this->e($new->title) . '" />';
                } else {
                    echo $new->title;
                }
                echo '</a></div>';
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

