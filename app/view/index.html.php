<?php

use Goteo\Core\View,
    Goteo\Model\Image,
    Goteo\Library\Text;

// si es un nodo
if (NODE_ID != GOTEO_NODE) {
    include 'view/node/index.html.php';
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


include 'view/prologue.html.php';
include 'view/header.html.php';
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
        <div class="mod-pojctopen"><?php echo Text::html('open-banner-header', $fbCode); ?></div>
    </div>
    <div class="sliderbanners-ctrl">
        <a class="prev">prev</a>
        <ul class="paginacion"></ul>
        <a class="next">next</a>
    </div>
</div>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

<div id="main">

    <?php foreach ($this['order'] as $item=>$itemData) {

        if ($item=="news")
            {
                $bannerPrensa = View::get('home/news.html.php',$this);
                continue;
            }

        if (!empty($this[$item])) echo View::get("view/home/{$item}.html.php", $this);
    } ?>

</div>

<?php include 'view/footer.html.php'; ?>
<?php include 'view/epilogue.html.php'; ?>
