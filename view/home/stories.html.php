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
                <div class="title_story"><strong><?php echo Text::get('home-stories-header').": "?></strong><span style="text-decoration:underline">Open Data</span></div>
                <div class="info">
                    <div style="font-size:18px;"><strong><?php echo htmlspecialchars($story->title); ?></strong></div>
                    <div style="margin-top:5px; color:#BCE8E8; font-size:15px;"><?php echo htmlspecialchars($story->name); ?></div>
                    <div style="border-bottom-style:solid; border-bottom-width:1px; width:15px; margin-top:7px; margin-bottom:7px;"></div>
                    <div style="color:#58595B"><?php echo htmlspecialchars($story->description); ?></div>
                </div>
                <div class="info_extra">
                    <span style="color:#313B96; font-size:11px;"><span style="position:absolute; bottom:16px;">COFINANCIADORES</span> <strong style="font-size:20px; margin-left:110px;">1000</strong></span>
                    <span style="color:#96238F; font-size:11px; margin-left:40px;"><span style="position:absolute; bottom:16px;">OBTENIDO</span><strong style="font-size:20px; margin-left:63px;">2.500</strong><img style=" vertical-align: text-bottom; margin-left:5px;" src="/view/css/euro/violet/xl.png" width="20"/></span>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
    <div id="stories-banners-controler"><ul class="bannerspage"></ul></div>
</div>

</div>
