<?php

use Goteo\Model\Image;

$this->layout("$theme::layout", ['meta_description' => $this->text('meta-description-index')]);


$bodyClass = 'home';
// para que el prologue ponga el código js para botón facebook en el bannerside
$fbCode = $this->text_widget($this->text('social-account-facebook'), 'fb');

// metas og: para que al compartir en facebook coja las imagenes de novedades
if (!empty($posts)) {
    $og_image = [];
    foreach ($posts as $post) {
        if (count($post->gallery) > 1) {
            foreach ($post->gallery as $pbimg) {
                if ($pbimg instanceof Image) {
                    $og_image[] = $pbimg->getLink(500, 285);
                }
            }
        } elseif ((!empty($post->image))&&($post->image instanceof Image)) {
            $og_image[] = $post->image->getLink(500, 285);
        }
    }

    $this->engine->addData(['image' => $og_image], "$theme::layout");
}

?>
<script type="text/javascript">
    $(function(){
        $('#sub-header').slides({
            play: 8000
        });
    });

</script>
<div id="sub-header" class="banners">
    <div class="clearfix">
        <div class="slides_container">
            <?php if (!empty($banners)) : foreach ($banners as $id=>$banner) : ?>
            <div class="subhead-banner"><?php $this->insert("$theme::partials/header/banner", ['banner' => $banner]); ?></div>
            <?php endforeach; endif;
            if (count($banners) == 1) : ?>
            <div class="subhead-banner"><?php echo $this->text_html('main-banner-header'); ?></div>
            <?php endif; ?>
        </div>
        <div class="mod-pojctopen" id="mod-pojctopen">
            <a href="" id="event-link" class="expand"></a>
            <div class="main-calendar" id="main-calendar">
                <div class="next-event">
                    <span><?php echo $this->text('calendar-home-title'); ?></span>
                </div>
                <div class="inside" id="inside">
                    <div class="event-month" id="event-month"></div>
                    <div class="event-day" id="event-day"></div>
                    <div id="event-text-day"></div>
                    <div class="event-interval"><span class="icon-clock"></span><span id ="event-start"></span><?php echo $this->text('calendar-home-hour'); ?><span id ="event-end"></span></div>
                </div>
            </div>
            <div class="extra-calendar" id="extra-calendar">
                <div class="event-category" id="event-category"></div>
                <div class="event-title" style="padding:10px; height:60px;" id="event-title"></div>
                <!--<span class="icon-ubication"></span>-->
                <span class="icon-ubication">
                <span class="path1"></span><span class="path2"></span>
                </span>
                <span id="event-location"></span>
            </div>
        </div>
    </div>
    <div class="sliderbanners-ctrl">
        <a class="prev">prev</a>
        <ul class="paginacion"></ul>
        <a class="next">next</a>
    </div>
</div>

<div id="main">

    <?php foreach ($order as $item=>$itemData) {

        if ($item=="news")
            {
                $bannerPrensa = $this->insert("$theme::home/partials/news", $this->vars);
                continue;
            }

        if (!empty($$item)) {
            $this->insert("$theme::home/partials/$item", $this->vars);
        }
    }

    ?>

</div>
