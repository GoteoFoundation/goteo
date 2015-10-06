<?php

use Goteo\Model\Image;

$posts = $this->posts;
$banners = $this->banners;
// metas og: para que al compartir en facebook coja las imagenes de novedades
// TODO: esto en el controlador
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
}
// print_r($og_image);die;
$this->layout('layout', [
    'bodyClass' => 'home',
    'meta_description' => $this->text('meta-description-index'),
    'image' => $og_image
    ]);


$this->section('sub-header');

?>
    <div id="sub-header" class="banners">
        <div class="clearfix">
            <div class="slides_container">
                <?php if (!empty($banners)) : foreach ($banners as $id=>$banner) : ?>
                <div class="subhead-banner"><?=$this->insert("partials/header/banner", ['banner' => $banner]); ?></div>
                <?php endforeach; endif;
                if (count($banners) == 1) : ?>
                <div class="subhead-banner"><?=$this->text_html('main-banner-header')?></div>
                <?php endif; ?>
            </div>

        <?= $this->supply('index-sub-header-right', '<img src="/goteo_logo.png" alt="Goteo">') ?>

        </div>
        <div class="sliderbanners-ctrl">
            <a class="prev">prev</a>
            <ul class="paginacion"></ul>
            <a class="next">next</a>
        </div>
    </div>
<?php $this->replace() ?>


<?php $this->section('content') ?>
    <div id="main">

     <?php
     foreach ($this->order as $item => $itemData) {

        if ($item !== 'news' && $this->order[$item]) {
            echo $this->insert("home/partials/$item");
        }
    }
    ?>

    </div>

<?php $this->replace() ?>


<?php if($this->order['news']): ?>
    <?php $this->section('footer-news') ?>

        <div id="press_banner">
        <?=$this->insert("home/partials/news")?>
        </div>

    <?php $this->replace() ?>
<?php endif ?>


<?php $this->section('footer') ?>


<div id="fb-root"></div>

<script type="text/javascript">

    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) {return;}
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/<?= $this->lang_locale() ?>/all.js#xfbml=1&appId=189133314484241";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    $(function(){
        $('#sub-header').slides({
            play: 8000
        });

        $('#learn').slides({
            container: 'slder_container',
            paginationClass: 'slderpag',
            generatePagination: false,
            play: 0
        });

        $('#calls').slides({
            container: 'slder_calls',
            generatePagination: false,
            play: 6000
        });

        $('#campaigns').slides({
            container: 'slder_campaigns',
            generatePagination: false,
            play: 6000
        });

        $('.scroll-pane').jScrollPane({showArrows: true});

        $('#slides_news').slides({
            container: 'slder_news',
            generatePagination: false,
            play: 30000
        });

        $('#slides_patrons').slides({
            container: 'slder_patrons',
            generatePagination: false,
            play: 0
        });

        $('#stories-banners').slides({
            container: 'stories-banners-container',
            paginationClass: 'bannerspage',
            generatePagination: true,
            effect: 'slide',
            play:8000
        });

    });


</script>

<?php $this->append() ?>
