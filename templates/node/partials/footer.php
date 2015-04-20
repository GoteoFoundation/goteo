<?php

use Goteo\Model\Category,
    Goteo\Model\Post,  // esto son entradas en portada o en footer
    Goteo\Model\Sponsor;

$lang = (LANG != 'es') ? '?lang='.LANG : '';

$posts = Post::getList('footer');
unset($posts[566]);


// la entrada de tutorial paso a paso
$steps = Post::get(566);
?>

   <div id="footer">
		<div class="w940">
        	<div class="block about" style="border-left:none;">
            	<span class="title"><a href="#">Goteo</a></span>
                <div style="margin-top:-5px" class="scroll-pane">
                <p><?=$this->text_html('node-footer-about')?></p></div>
            </div>

            <div class="block help">
                <span class="title"><?=$this->text('footer-header-resources')?></span>
                <div>
                	<ul class="scroll-pane">
                    <li><a href="/faq"><?=$this->text('regular-header-faq')?></a></li>
                    <li><a href="/glossary"><?=$this->text('footer-resources-glossary')?></a></li>
                    <li><a href="/service/resources"><?=$this->text('footer-service-resources')?></a></li>
                    <li><a href="/service/workshop"><?=$this->text('footer-service-workshop')?></a></li>
                    <li><a href="https://github.com/Goteo/Goteo" target="_blank"><?=$this->text('footer-resources-source_code')?></a></li>
                    </ul>
                </div>
            </div>

            <div class="block creators">
                <span class="title"><?=$this->text('node-footer-title-creators')?></span>
                <ul class="scroll-pane">
                    <?php foreach ($posts as $id=>$title) : ?>
                    <li><a href="/blog/<?php echo $id ?>"><?=$this->text_recorta($title, 50)?></a></li>
                    <?php endforeach ?>
                </ul>
            </div>

            <div class="block investors">
                <span class="title"><?=$this->text('node-footer-title-investors')?></span>
                <ul class="scroll-pane">
                    <?php if ($steps instanceof Post) : ?>
                    <li><a href="/blog/566"><?php echo $steps->title ?></a></li>
                    <?php endif ?>
                    <li><a href="/about/donations"><?=$this->text('node-footer-investors-donate')?></a></li>
                 </ul>
            </div>

            <div class="block social">
                <span class="title"><?=$this->text('node-footer-title-social')?></span>
                <ul class="scroll-pane">
                    <li class="twitter"><a href="<?=$this->text('social-account-twitter')?>" target="_blank"><?=$this->text('regular-twitter')?></a></li>
                    <li class="facebook"><a href="<?=$this->text('social-account-facebook')?>" target="_blank"><?=$this->text('regular-facebook')?></a></li>
                    <li class="calendar"><a href="<?=$this->text('social-account-identica')?>" target="_blank"><?=$this->text('regular-calendar')?></a></li>
                    <li class="gplus"><a href="<?=$this->text('social-account-google')?>" target="_blank"><?=$this->text('regular-google')?></a></li>
                    <li class="rss"><a rel="alternate" type="application/rss+xml" title="RSS" href="/rss<?php echo $lang ?>" target="_blank"><?=$this->text('regular-share-rss')?></a></li>

                </ul>
            </div>

		</div>
    </div>

    <div id="sub-footer">
		<div class="w940">

                <ul>
                    <li><a href="<?php echo SITE_URL ?>/about">Goteo.org</a></li>
                    <li><a href="/user/login"><?=$this->text('regular-login')?></a></li>
                    <li><a href="/blog"><?=$this->text('regular-header-blog')?></a></li>
                    <li><a href="/press"><?=$this->text('footer-resources-press')?></a></li>
                    <li><a href="/legal/privacy"><?=$this->text('regular-footer-privacy')?></a></li>
                    <li><a href="/contact"><?=$this->text('regular-footer-contact')?></a></li>
                </ul>

                <div class="platoniq">
                   <span class="text"><a href="#" class="poweredby"><?=$this->text('footer-platoniq-iniciative')?></a></span>
                   <span class="logo"><a href="http://fuentesabiertas.org" target="_blank" class="foundation">FFA</a></span>
                   <span class="logo"><a href="http://www.youcoop.org" target="_blank" class="growby">Platoniq</a></span>
                </div>


        </div>

    </div>
