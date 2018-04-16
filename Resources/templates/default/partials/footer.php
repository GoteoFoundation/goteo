<?php

$categories = $this->model_static('Category', 'getNames');
$posts = $this->model_static('Post', 'getList', 'footer');
$sponsors = $this->model_static('Sponsor', 'getList', 'goteo');

?>

<div id="footer">

    <?php $this->section('footer-news') ?>
    <?php $this->stop() ?>

    <div class="w940" style="padding:20px;">

        <?php $this->section('footer-categories') ?>
        <div class="block categories">
            <h6 class="title"><?=$this->text('footer-header-categories') ?></h6>
            <ul class="scroll-pane jspScrollable">
            <?php foreach ($categories as $id=>$name) : ?>
                <li><a href="/discover/results/<?php echo $id.'/'.$name; ?>"><?php echo $name; ?></a></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php $this->stop() ?>

        <?php $this->section('footer-project') ?>
        <div class="block projects">
            <h6 class="title"><?=$this->text('footer-header-projects') ?></h6>
            <ul class="scroll-pane">
                <li><a href="/"><?=$this->text('home-promotes-header') ?></a></li>
                <li><a href="/discover/view/popular"><?=$this->text('discover-group-popular-header') ?></a></li>
                <li><a href="/discover/view/outdate"><?=$this->text('discover-group-outdate-header') ?></a></li>
                <li><a href="/discover/view/recent"><?=$this->text('discover-group-recent-header') ?></a></li>
                <li><a href="/discover/view/success"><?=$this->text('discover-group-success-header') ?></a></li>
                <li><a href="/discover/view/fulfilled"><?=$this->text('discover-group-fulfilled-header') ?></a></li>
                <li><a href="/discover/view/archive"><?=$this->text('discover-group-archive-header') ?></a></li>
                <li><a href="/project/create"><?=$this->text('regular-create') ?></a></li>
            </ul>
        </div>
        <?php $this->stop() ?>

        <?php $this->section('footer-resources') ?>
        <div class="block resources">
            <h6 class="title"><?=$this->text('footer-header-resources') ?></h6>
            <ul class="scroll-pane">
                <li><a href="/faq"><?=$this->text('regular-header-faq') ?></a></li>
                <li><a href="/glossary"><?=$this->text('footer-resources-glossary') ?></a></li>
                <?php foreach ($posts as $id => $title) : ?>
                <li><a href="/blog/<?php echo $id ?>"><?=$this->text_truncate($title, 50)?></a></li>
                <?php endforeach ?>
                <li><a href="/newsletter" target="_blank">Newsletter</a></li>
                <li><a href="https://github.com/GoteoFoundation/goteo" target="_blank"><?=$this->text('footer-resources-source_code') ?></a></li>
                <li><a rel="jslicense" href="/about/librejs">Licenses</a></li>
            </ul>
        </div>
        <?php $this->stop() ?>

        <?php $this->section('footer-sponsors') ?>
        <div id="slides_sponsor" class="block sponsors">
            <h6 class="title"><?=$this->text('footer-header-sponsors') ?></h6>
            <div class="slides_container">
                <?php $i = 1; foreach ($sponsors as $sponsor) : ?>
                <div class="sponsor" id="footer-sponsor-<?php echo $i ?>">
                    <a href="<?php echo $sponsor->url ?>" title="<?php echo $sponsor->name ?>" target="_blank" rel="nofollow"><img src="<?php echo $sponsor->image->getLink(150, 85) ?>" alt="<?=$this->e($sponsor->name) ?>" /></a>
                </div>
                <?php $i++; endforeach; ?>
            </div>
            <div class="slidersponsors-ctrl">
                <a class="prev">prev</a>
                <ul class="paginacion"></ul>
                <a class="next">next</a>
            </div>
        </div>
        <?php $this->stop() ?>

        <?php $this->section('footer-services') ?>
        <div class="block services">

            <h6 class="title"><?=$this->text('footer-header-services') ?></h6>
            <ul>
            <?php if($this->get_config('payments.pool.active')): ?>
                <li><a href="<?= ($this->is_logged()) ? '/dashboard/wallet' : '/pool' ?>"><img class="icon pool" src="<?= SRC_URL . '/view/css/services/pool_icon.png' ?>" height="20" /><?=$this->text('footer-service-pool') ?></a></li>
            <?php endif ?>
            </ul>

        </div>
        <?php $this->stop() ?>

        <?php $this->section('footer-social') ?>
        <div class="block social" style="border-right:#ebe9ea 2px solid;">
            <h6 class="title"><?=$this->text('footer-header-social') ?></h6>
            <ul>
                <li class="twitter"><a href="<?=$this->text('social-account-twitter') ?>" target="_blank"><?=$this->text('regular-twitter') ?></a></li>
                <li class="facebook"><a href="<?=$this->text('social-account-facebook') ?>" target="_blank"><?=$this->text('regular-facebook') ?></a></li>
                <li class="gplus"><a href="<?=$this->text('social-account-google') ?>" target="_blank"><?=$this->text('regular-google') ?></a></li>
                <li class="rss"><a rel="alternate" type="application/rss+xml" title="RSS" href="/rss/<?= $this->lang_current() ?>" target="_blank"><?=$this->text('regular-share-rss')?></a></li>

            </ul>
        </div>
        <?php $this->stop() ?>

	</div>
</div>

<div id="sub-footer">
	<div class="w940">

            <ul>
                <li><a href="/about"><?=$this->text('regular-header-about')?></a></li>
                <li><a href="/user/login"><?=$this->text('regular-login')?></a></li>
                <li><a href="/contact"><?=$this->text('regular-footer-contact')?></a></li>
<!--                    <li><a href="/blog"><?=$this->text('regular-header-blog')?></a></li> -->
<!--                    <li><a href="/about/legal"><?=$this->text('regular-footer-legal')?></a></li> -->
                <li><a href="/legal/terms"><?=$this->text('regular-footer-terms')?></a></li>
                <li><a href="/legal/privacy"><?=$this->text('regular-footer-privacy')?></a></li>
            </ul>

            <div class="platoniq">
               <span class="text"><a href="#" class="poweredby"><?=$this->text('footer-platoniq-iniciative') ?></a></span>
               <span class="logo"><a href="http://fundacion.goteo.org" target="_blank" class="foundation">Fundación Goteo</a></span>
               <span class="logo"><a href="http://www.platoniq.net" target="_blank" class="growby">Platoniq</a></span>
            </div>


    </div>

</div>
