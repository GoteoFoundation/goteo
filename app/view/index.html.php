<?php

use Goteo\Core\View,
    Goteo\Model\Image,
    Goteo\Library\Text;

// si es un nodo
if (NODE_ID != GOTEO_NODE) {
    include __DIR__ . '/node/index.html.php';
    return;
}


$calls     = $this['calls'];
$campaigns = $this['campaigns'];

$bodyClass = 'home';
// para que el prologue ponga el código js para botón facebook en el bannerside
$fbCode = Text::widget(Text::get('social-account-facebook'), 'fb');

// metas og: para que al compartir en facebook coja las imagenes de novedades
$ogmeta = array(
    'title' => 'Goteo.org',
    'description' => GOTEO_META_DESCRIPTION,
    'url' => SITE_URL
);
if (!empty($this['posts'])) {
    foreach ($this['posts'] as $post) {
        if (count($post->gallery) > 1) {
            foreach ($post->gallery as $pbimg) {
                if ($pbimg instanceof Image) {
                    $ogmeta['image'][] = $pbimg->getLink(500, 285);
                }
            }
        } elseif ((!empty($post->image))&&($post->image instanceof Image)) {
            $ogmeta['image'][] = $post->image->getLink(500, 285);
        }
    }
}


include __DIR__ . '/prologue.html.php';
include __DIR__ . '/header.html.php';
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
            <?php if (!empty($this['banners'])) : foreach ($this['banners'] as $id=>$banner) : ?>
            <div class="subhead-banner"><?php echo View::get('header/banner.html.php', array('banner'=>$banner)); ?></div>
            <?php endforeach; endif;
            if (count($this['banners']) == 1) : ?>
            <div class="subhead-banner"><?php echo Text::html('main-banner-header'); ?></div>
            <?php endif; ?>
        </div>
        <div class="mod-pojctopen" id="mod-pojctopen">
            <a href="" id="event-link" class="expand"></a>
            <div class="main-calendar">
                <div class="next-event">
                    <span><?php echo Text::get('calendar-home-title'); ?></span>
                </div>
                <div class="inside">
                    <div class="event-month" id="event-month"></div>
                    <div class="event-day" id="event-day"></div>
                    <div id="event-text-day"></div>
                    <div class="event-interval"><span class="icon-clock"></span><span id ="event-start"></span><?php echo Text::get('calendar-home-hour'); ?><span id ="event-end"></span></div>
                </div>
            </div>
            <div class="extra-calendar" class="extra-calendar">
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

<?php if(isset($_SESSION['messages'])) { include __DIR__ . '/header/message.html.php'; } ?>

<div id="main">

    <?php foreach ($this['order'] as $item=>$itemData) {

        if ($item=="news")
            {
                $bannerPrensa = View::get('home/news.html.php',$this);
                continue;
            }

        if (!empty($this[$item])) echo View::get("home/{$item}.html.php", $this);
    } ?>

</div>

<?php include __DIR__ . '/footer.html.php'; ?>
<?php include __DIR__ . '/epilogue.html.php'; ?>
