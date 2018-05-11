<?php

if($this->post->image)

	$meta_img= $this->post->image->getlink(400, 0, false, true);

elseif($this->post->header_image)
	$meta_img= $this->post->header_image->getlink(400, 0, false, true);

else
	$meta_img= $this->asset('img/blog/header_default.png');


$this->layout('layout', [
    'tw_image' =>  $meta_img
    ]);

?>

<?php $this->section('head'); ?>

    <?= $this->insert('blog/partials/styles'); ?>

<?php $this->append(); ?>

<?php $this->section('header-navbar-brand'); ?>

    <a class="navbar-brand" href="<?= $this->get_config('url.main') ?>"><img src="<?= $this->asset('img/icons/foundation.svg') ?>" class="logo" alt="Goteo"></a>
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

        $('.slider-main').slick({
            dots: true,
            infinite: true,
            autoplay: false,
            autoplaySpeed: 7000,
            speed: 1500,
            fade: true,
            arrows: true,
            cssEase: 'linear',
            prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
            nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
        });
    });
</script>

<?php $this->append() ?>

