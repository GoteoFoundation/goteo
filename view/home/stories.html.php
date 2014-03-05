<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$stories = $this['stories'];
// random y que solo pinte seis si hubiera más
if (count($stories) > 6) {
	shuffle($stories);
	$stories = array_slice($stories, 0, 6);
}
?>
<div class="widget stories-home" style="padding:0;">

    

	<script type="text/javascript">
    $(function(){
        $('#stories-banners').slides({
            container: 'stories-banners-container',
            paginationClass: 'bannerspage',
            generatePagination: true,
            effect: 'fade',
            fadeSpeed: 200
        });
    });
</script>
<div id="stories-banners" class="rounded-corners-bottom">
    <div class="stories-banners-container rounded-corners-bottom" style="background: url('/view/css/call/banner_background.png');">

        <?php foreach ($stories as $story) : ?>
            <div class="stories-banner<?php if (!empty($story->url)) echo ' activable'; ?>"<?php if ($story->image instanceof \Goteo\Model\Image) : ?> style="background: url('/data/images/<?php echo $story->image->name; ?>');"<?php endif; ?>>
                <?php if (!empty($story->url)) : ?><a href="<?php echo $story->url; ?>" class="expand" target="_blank"></a><?php endif; ?>
                <div class="title_story"><?php echo Text::get('home-stories-header'); ?></div>
                <div class="info">
                    <div><?php echo htmlspecialchars($story->title); ?></div>
                    <div style="margin-top:5px;"><?php echo htmlspecialchars($story->description); ?>Descripción de Architecture in your hand. Architecture in your hand. Architecture in your hand.
ARCHINHAND | Architecture in your</div>
                    <div style="margin-top:5px;"><?php echo htmlspecialchars($story->name); ?></div>
                    <div><?php echo htmlspecialchars($story->node); ?></div>
                </div>
                <div class="info_extra">
                    <span style="color:#313B96; font-size:12px;">COFINANCIADORES <strong style="font-size:20px;">80</strong></span>
                    <span style="color:#96238F; font-size:12px; margin-left:10px;">OBTENIDO <strong style="font-size:20px;">2.500</strong><img style="vertical-align: middle; margin-left:5px;" src="/view/css/euro/violet/xl.png" width="20"/></span>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
    <div id="stories-banners-controler"><ul class="bannerspage"></ul></div>
</div>

</div>
