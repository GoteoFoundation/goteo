<?php

if($this->post->image)

	$meta_img= $this->post->image->getlink(400, 0, false, true);

elseif($this->post->header_image)
	$meta_img= $this->post->header_image->getlink(400, 0, false, true);

else
	$meta_img= $this->asset('img/blog/header_default.png');


$this->layout('layout', [
    'bodyClass' => 'blog',
    'title' =>  $this->post->title,
    'meta_description' => $this->post->subtitle ? $this->post->subtitle : $this->text_truncate($this->post->text, 100),
    'tw_image' =>  $meta_img
    ]);



$this->section('head'); ?>

<link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick-theme.css"/>
<link href="<?= SRC_URL ?>/assets/css/typeahead.css" rel="stylesheet">

<link rel="stylesheet" href="/assets/vendor/selection-sharer/selection-sharer.css" />


<?php 

//$this->insert('blog/partials/styles');

$this->append();



$this->section('header-navbar-brand');

?>
    <a class="navbar-brand" href="<?= $this->get_config('url.main') ?>"><img src="<?= $this->asset('/img/icons/foundation.svg') ?>" class="logo" alt="Goteo"></a>
    <h3><?= $this->text('home-foundation-title') ?></h3>

<?php

$this->replace();

$this->section('content');

?>

<div class="blog">

    <?= $this->supply('blog-content') ?>

</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<?= $this->insert('blog/partials/javascript') ?>

<script>
    $(function(){
    	$('div').selectionSharer();

        $('.slider-team').slick({
            dots: false,
            autoplay: true,
            infinite: true,
            speed: 2000,
            autoplaySpeed: 3000,
            fade: true,
            arrows: false,
            cssEase: 'linear'
        });
    });
</script>

<?php $this->append() ?>

