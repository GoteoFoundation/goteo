<?php
use Goteo\Core\View;

$stories = $this->stories;

if(!is_array($stories)) return;
?>

<div class="widget stories-home" style="padding:0;">

<div id="stories-banners" class="rounded-corners-bottom">
    <div class="stories-banners-container rounded-corners-bottom" style="max-height:383px; overflow:hidden;">

        <?php foreach ($stories as $story) :

        echo View::get('stories/story.html.php', array('story'=>$story));

        endforeach; ?>

    </div>
    <div id="stories-banners-controler"><ul class="bannerspage"></ul></div>
</div>

</div>

