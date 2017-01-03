<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$stories = $vars['stories'];

?>
<div class="widget stories-home" style="padding:0;">

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function(){
    $('#stories-banners').slides({
        container: 'stories-banners-container',
        paginationClass: 'bannerspage',
        generatePagination: true,
        effect: 'slide',
        play:12000
    });
});
// @license-end
</script>
<div id="stories-banners" class="rounded-corners-bottom">
    <div class="stories-banners-container rounded-corners-bottom" style="max-height:383px; overflow:hidden;">

        <?php foreach ($stories as $story) :

        echo View::get('stories/story.html.php', array('story'=>$story));

        endforeach; ?>

    </div>
    <div id="stories-banners-controler"><ul class="bannerspage"></ul></div>
</div>

</div>
