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
<div class="stories-home">

    <h2 class="title"><?php echo Text::get('home-stories-header'); ?></h2>

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
                <div class="info">
                    <div><?php echo htmlspecialchars($story->title); ?></div>
                    <div style="margin-top:5px;"><?php echo htmlspecialchars($story->description); ?></div>
                    <div style="margin-top:5px;"><?php echo htmlspecialchars($story->name); ?></div>
                    <div><?php echo htmlspecialchars($story->node); ?></div>
                </div>
                <div class="info_right">
                    <div>15 cofinanciadores</div>
                    <div style="margin-top:5px;">3000 € conseguidos</div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
    <div id="stories-banners-controler"><ul class="bannerspage"></ul></div>
</div>

</div>
