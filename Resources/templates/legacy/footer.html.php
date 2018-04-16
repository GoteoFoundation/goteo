<?php

use Goteo\Library\Text,
    Goteo\Model\Category,
    Goteo\Model\Post,  // esto son entradas en portada o en footer
    Goteo\Model\Sponsor,
    Goteo\Application\Config;


if (Config::get('alternate.footer')) {
    include Config::get('alternate.footer');
    return;
}

$lang = (LANG != 'es') ? '?lang='.LANG : '';

//activamos la cache para las consultas de categorias, posts, sponsors
\Goteo\Core\DB::cache(true);
$categories = Category::getNames();  // categorias que se usan en proyectos
$posts      = Post::getList('footer');
$sponsors   = Sponsor::getList();

//echo \trace($sponsors);
?>


    <div id="footer">
        <?php if( $bannerPrensa && count($vars['news']) ) {?>
        <div id="press_banner">
            <?php echo $bannerPrensa;?>
        </div>
        <?php }?>
		<div class="w940" style="padding:20px;">
        	<div class="block categories">
                <h6 class="title"><?php echo Text::get('footer-header-categories') ?></h6>
                <ul class="scroll-pane">
                <?php foreach ($categories as $id=>$name) : ?>
                    <li><a href="/discover/results/<?php echo $id.'/'.$name; ?>"><?php echo $name; ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>

            <div class="block projects">
                <h6 class="title"><?php echo Text::get('footer-header-projects') ?></h6>
                <ul class="scroll-pane">
                    <li><a href="/"><?php echo Text::get('home-promotes-header') ?></a></li>
                    <li><a href="/discover/view/popular"><?php echo Text::get('discover-group-popular-header') ?></a></li>
                    <li><a href="/discover/view/outdate"><?php echo Text::get('discover-group-outdate-header') ?></a></li>
                    <li><a href="/discover/view/recent"><?php echo Text::get('discover-group-recent-header') ?></a></li>
                    <li><a href="/discover/view/success"><?php echo Text::get('discover-group-success-header') ?></a></li>
                    <li><a href="/discover/view/fulfilled"><?php echo Text::get('discover-group-fulfilled-header') ?></a></li>
                    <li><a href="/discover/view/archive"><?php echo Text::get('discover-group-archive-header') ?></a></li>
                    <li><a href="/project/create"><?php echo Text::get('regular-create') ?></a></li>
                </ul>
            </div>

            <div class="block resources">
                <h6 class="title"><?php echo Text::get('footer-header-resources') ?></h6>
                <ul class="scroll-pane">
                    <li><a href="http://developers.goteo.org" target="_blank"><?php echo Text::get('footer-resources-api') ?></a></li>
                    <li><a href="http://stats.goteo.org" target="_blank"><?php echo Text::get('footer-resources-stats') ?></a></li>
                    <li><a href="/faq"><?php echo Text::get('regular-header-faq') ?></a></li>
                    <li><a href="/glossary"><?php echo Text::get('footer-resources-glossary') ?></a></li>
                    <li><a href="/press"><?php echo Text::get('footer-resources-press') ?></a></li>
                    <?php foreach ($posts as $id=>$title) : ?>
                    <li><a href="/blog/<?php echo $id ?>"><?php echo Text::recorta($title, 50) ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="/newsletter" target="_blank">Newsletter</a></li>
                    <li><a href="https://github.com/GoteoFoundation/goteo" target="_blank"><?php echo Text::get('footer-resources-source_code') ?></a></li>
                    <li><a rel="jslicense" href="/about/librejs">Licenses</a></li>
                </ul>
            </div>

           <div id="slides_sponsor" class="block sponsors">
                <h6 class="title"><?php echo Text::get('footer-header-sponsors') ?></h6>
				<div class="slides_container">
					<?php $i = 1; foreach ($sponsors as $sponsor) : ?>
					<div class="sponsor" id="footer-sponsor-<?php echo $i ?>">
						<a href="<?php echo $sponsor->url ?>" title="<?php echo $sponsor->name ?>" target="_blank" rel="nofollow"><img src="<?php echo $sponsor->image->getLink(150, 85) ?>" alt="<?php echo htmlspecialchars($sponsor->name) ?>" /></a>
					</div>
					<?php $i++; endforeach; ?>
				</div>
				<div class="slidersponsors-ctrl">
					<a class="prev">prev</a>
					<ul class="paginacion"></ul>
					<a class="next">next</a>
				</div>
            </div>

            <div class="block services">

                <h6 class="title"><?php echo Text::get('footer-header-services') ?></h6>

                <ul>
                    <li><a href="/service/resources"><img class="icon" src="<?= SRC_URL . '/view/css/services/call_icon.png' ?>" height="20" /><?= Text::get('footer-service-resources') ?></a></li>
                    <li><a href="/service/workshop"><img class="icon" src="<?= SRC_URL . '/view/css/services/workshops_icon.png' ?>" height="20" /><?= Text::get('footer-service-workshop') ?></a></li>
                    <li><a href="/calculadora-fiscal"><img class="icon calculator" src="<?= SRC_URL . '/view/css/services/calculator_icon.png' ?>" height="20" /><?= Text::get('footer-service-calculator') ?></a></li>
                    <li><a href="/pool"><img class="icon pool" src="<?= SRC_URL . '/view/css/services/pool_icon.png' ?>" height="20" /><?= Text::get('footer-service-pool') ?></a></li>
                    <li><a href="http://stats.goteo.org" target="_blank"><img class="icon stats" src="<?= SRC_URL . '/view/css/services/stats.png' ?>" height="20" /><?= Text::get('footer-resources-stats') ?></a></li>

                </ul>

            </div>

            <div class="block social" style="border-right:#ebe9ea 2px solid;">
                <h6 class="title"><?php echo Text::get('footer-header-social') ?></h6>
                <ul>
                    <li class="twitter"><a href="<?php echo Text::get('social-account-twitter') ?>" target="_blank"><?php echo Text::get('regular-twitter') ?></a></li>
                    <li class="facebook"><a href="<?php echo Text::get('social-account-facebook') ?>" target="_blank"><?php echo Text::get('regular-facebook') ?></a></li>
                    <li class="calendar"><a href="/calendar" target="_blank"><?php echo Text::get('regular-calendar') ?></a></li>
                    <li class="gplus"><a href="<?php echo Text::get('social-account-google') ?>" target="_blank"><?php echo Text::get('regular-google') ?></a></li>
                    <li class="rss"><a rel="alternate" type="application/rss+xml" title="RSS" href="/rss<?php echo $lang ?>" target="_blank"><?php echo Text::get('regular-share-rss'); ?></a></li>

                </ul>
            </div>

		</div>
    </div>

    <div id="sub-footer">
		<div class="w940">



                <ul>
                    <li><a href="/about"><?php echo Text::get('regular-header-about'); ?></a></li>
                    <li><a href="/user/login"><?php echo Text::get('regular-login'); ?></a></li>
                    <li><a href="/contact"><?php echo Text::get('regular-footer-contact'); ?></a></li>
<!--                    <li><a href="/blog"><?php echo Text::get('regular-header-blog'); ?></a></li> -->
<!--                    <li><a href="/about/legal"><?php echo Text::get('regular-footer-legal'); ?></a></li> -->
                    <li><a href="/legal/terms"><?php echo Text::get('regular-footer-terms'); ?></a></li>
                    <li><a href="/legal/privacy"><?php echo Text::get('regular-footer-privacy'); ?></a></li>
                </ul>

                <div class="platoniq">
                   <span class="text"><a href="#" class="poweredby"><?php echo Text::get('footer-platoniq-iniciative') ?></a></span>
                   <span class="logo"><a href="http://fundacion.goteo.org/" target="_blank" class="foundation">Fundación Goteo</a></span>
                   <span class="logo"><a href="http://www.platoniq.net" target="_blank" class="growby">Platoniq</a></span>
                </div>


        </div>

    </div>
