<?php

use Goteo\Library\Text,
    Goteo\Model\Category,
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
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('.scroll-pane').jScrollPane({showArrows: true});
			});
			</script>
        	<div class="block about" style="border-left:none;">
            	<span class="title"><a href="#">Goteo</a></span>
                <div style="margin-top:-5px" class="scroll-pane">
                <p><?php echo Text::html('node-footer-about') ?></p></div>
            </div>

            <div class="block help">
                <span class="title"><?php echo Text::get('footer-header-resources') ?></span>
                <div>
                	<ul class="scroll-pane">
                    <li><a href="/faq"><?php echo Text::get('regular-header-faq') ?></a></li>
                    <li><a href="/glossary"><?php echo Text::get('footer-resources-glossary') ?></a></li>
                    <li><a href="/service/resources"><?php echo Text::get('footer-service-resources') ?></a></li>
                    <li><a href="/service/workshop"><?php echo Text::get('footer-service-workshop') ?></a></li>
                    <li><a href="https://github.com/Goteo/Goteo" target="_blank"><?php echo Text::get('footer-resources-source_code') ?></a></li>
                    </ul>
                </div>
            </div>

            <div class="block creators">
                <span class="title"><?php echo Text::get('node-footer-title-creators') ?></span>
                <ul class="scroll-pane">
                    <?php foreach ($posts as $id=>$title) : ?>
                    <li><a href="/blog/<?php echo $id ?>"><?php echo Text::recorta($title, 50) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="block investors">
                <span class="title"><?php echo Text::get('node-footer-title-investors') ?></span>
                <ul class="scroll-pane">
                    <?php if ($steps instanceof Post) : ?>
                    <li><a href="/blog/566"><?php echo $steps->title ?></a></li>
                    <?php endif; ?>
                    <li><a href="/about/donations"><?php echo Text::get('node-footer-investors-donate') ?></a></li>
                 </ul>
            </div>

            <div class="block social">
                <span class="title"><?php echo Text::get('node-footer-title-social') ?></span>
                <ul class="scroll-pane">
                    <!--
                    <li class="twitter"><a href="<?php echo Text::get('social-account-twitter') ?>" target="_blank"><?php echo Text::get('regular-twitter') ?></a></li>
                    <li class="facebook"><a href="<?php echo Text::get('social-account-facebook') ?>" target="_blank"><?php echo Text::get('regular-facebook') ?></a></li>
                    <li class="calendar"><a href="<?php echo Text::get('social-account-identica') ?>" target="_blank"><?php echo Text::get('regular-calendar') ?></a></li>
                    <li class="gplus"><a href="<?php echo Text::get('social-account-google') ?>" target="_blank"><?php echo Text::get('regular-google') ?></a></li>
                    <li class="rss"><a rel="alternate" type="application/rss+xml" title="RSS" href="/rss<?php echo $lang ?>" target="_blank"><?php echo Text::get('regular-share-rss'); ?></a></li>
                    -->

                    <li class="twitter"><a href="<?= Text::get('social-account-twitter') ?>" target="_blank"><?= Text::get('regular-twitter') ?></a></li>
                    <li class="facebook"><a href="<?= Text::get('social-account-facebook') ?>" target="_blank"><?= Text::get('regular-facebook') ?></a></li>
                    <li class="instagram"><a href="<?= Text::get('social-account-instagram') ?>" target="_blank"><?= Text::get('regular-instagram')?></a></li>
                    <li class="calendar"><a href="/calendar" target="_blank"><?= Text::get('regular-calendar') ?></a></li>
                    <li class="gplus"><a href="<?= Text::get('social-account-google') ?>" target="_blank"><?= Text::get('regular-google') ?></a></li>
                    <li class="rss"><a rel="alternate" type="application/rss+xml" title="RSS" href="/rss<?= $lang ?>" target="_blank"><?= Text::get('regular-share-rss')?></a></li>
                </ul>
            </div>

		</div>
    </div>

    <div id="sub-footer">
		<div class="w940">

                <ul>
                    <li><a href="<?php echo SITE_URL ?>/about">Goteo.org</a></li>
                    <li><a href="/user/login"><?php echo Text::get('regular-login'); ?></a></li>
                    <li><a href="/blog"><?php echo Text::get('regular-header-blog'); ?></a></li>
                    <li><a href="/press"><?php echo Text::get('footer-resources-press'); ?></a></li>
                    <li><a href="/legal/privacy"><?php echo Text::get('regular-footer-privacy'); ?></a></li>
                    <li><a href="/contact"><?php echo Text::get('regular-footer-contact'); ?></a></li>
                </ul>

                <div class="platoniq">
                   <span class="text"><a href="#" class="poweredby"><?php echo Text::get('footer-platoniq-iniciative') ?></a></span>
                   <span class="logo"><a href="http://fundacion.goteo.org/" target="_blank" class="foundation">FFA</a></span>
                   <span class="logo"><a href="http://www.youcoop.org" target="_blank" class="growby">Platoniq</a></span>
                </div>


        </div>

    </div>
