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
    <div class="stories-banners-container rounded-corners-bottom">

        <?php foreach ($stories as $story) : ?>
            <div class="stories-banner<?php if (!empty($story->url)) echo ' activable'; ?>"<?php if ($story->image instanceof \Goteo\Model\Image) : ?> style="background: url('/data/images/<?php echo $story->image->name; ?>');"<?php endif; ?>>
                <?php if (!empty($story->url)) : ?><a href="<?php echo $story->url; ?>" class="expand" target="_blank"></a><?php endif; ?>
                <div class="title_story"><strong><?php echo Text::get('home-stories-header').': '?></strong><span style="text-decoration:underline">Open Data</span></div>
                <div class="info">
                    <div id="info_title"><?php echo htmlspecialchars($story->title); ?></div>
                    <div id="review"><?php echo htmlspecialchars($story->review); ?></div>
                    <div id="line"></div>
                    <div id="description"><blockquote><?php echo htmlspecialchars($story->description).'</blockquote>. '.'<a href="/user/profile/'.$story->project->user->id.'">'.htmlspecialchars($story->project->user->name).'</a>, del proyecto: '.'<a href="/project/'.$story->project->id.'">'.htmlspecialchars($story->project->name).'</a>'; ?></div>
                </div>
                <div class="info_extra">
                    <span id="cofinanciadores"><?php echo  mb_strtoupper(Text::get('project-view-metter-investors'));?></span> <strong id="ncofinanciadores"><?php echo $story->project->num_investors;?></strong>
                    <span id="obtenido"><span><?php echo  mb_strtoupper(Text::get('project-view-metter-got'));?></span><strong><?php echo $story->project->amount;?></strong><img src="/view/css/euro/violet/xl.png" width="20"/></span>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
    <div id="stories-banners-controler"><ul class="bannerspage"></ul></div>
</div>

</div>
