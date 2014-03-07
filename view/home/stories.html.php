<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$stories = $this['stories'];
    
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
                    <div id="info_title"><?php echo mb_strtoupper(htmlspecialchars($story->title)); ?></div>
                    <div id="review"><?php echo htmlspecialchars($story->review); ?></div>
                    <div id="line"></div>
                    <div id="description"><blockquote><?php echo htmlspecialchars($story->description).'</blockquote>. '.'<span class="underline">David Fernández</span>'.', del proyecto: '.'<span class="underline">Mini clínica de termoterapia para la tercera edad</span>'; ?></div>
                </div>
                <div class="info_extra">
                    <span id="cofinanciadores"><?php echo  mb_strtoupper(Text::get('project-view-metter-investors'));?></span> <strong id="ncofinanciadores">1000</strong>
                    <span id="obtenido"><span><?php echo  mb_strtoupper(Text::get('project-view-metter-got'));?></span><strong>2.500</strong><img src="/view/css/euro/violet/xl.png" width="20"/></span>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
    <div id="stories-banners-controler"><ul class="bannerspage"></ul></div>
</div>

</div>
