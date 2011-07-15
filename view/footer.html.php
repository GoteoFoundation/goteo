<?php

use Goteo\Library\Text,
    Goteo\Model\Category,
    Goteo\Model\Post,
    Goteo\Model\Sponsor;

$categories = Category::getList();  // categorias que se usan en proyectos
$posts      = Post::getList('footer');
$sponsors   = Sponsor::getList();
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('.scroll-pane').jScrollPane({showArrows: true});
});
</script>

    <div id="footer">
		<div class="w940">
        	<div class="block categories">
                <h8 class="title"><?php echo Text::get('footer-header-categories') ?></h8>
                <ul class="scroll-pane">
                <?php foreach ($categories as $id=>$name) : ?>
                    <li><a href="/discover/results/<?php echo $id; ?>"><?php echo $name; ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>

            <div class="block projects">
                <h8 class="title"><?php echo Text::get('footer-header-projects') ?></h8>
                <ul>
                    <li><a href="/"><?php echo Text::get('home-promotes-header') ?></a></li>
                    <li><a href="/discover/view/popular"><?php echo Text::get('discover-group-popular-header') ?></a></li>
                    <!--<li><a href="/discover/view/outdate"><?php echo Text::get('discover-group-outdate-header') ?></a></li>-->
                    <li><a href="/discover/view/outdate"><?php echo Text::recorta(Text::get('discover-group-outdate-header'), 24) ?></a></li>
                    
                    <li><a href="/discover/view/recent"><?php echo Text::get('discover-group-recent-header') ?></a></li>
                    <li><a href="/discover/view/success"><?php echo Text::get('discover-group-success-header') ?></a></li>
                    <li><a href="/project/create"><?php echo Text::get('regular-create') ?></a></li>
                </ul>
            </div>

            <div class="block resources">
                <h8 class="title"><?php echo Text::get('footer-header-resources') ?></h8>
                <ul class="scroll-pane">
                    <li><a href="/faq"><?php echo Text::get('regular-header-faq') ?></a></li>
                    <?php foreach ($posts as $id=>$title) : ?>
                    <li><a href="/blog/<?php echo $id ?>"><?php echo Text::recorta($title, 50) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="block social">
                <h8 class="title"><?php echo Text::get('footer-header-social') ?></h8>
                <ul>
                    <li class="twitter"><a href="http://twitter.com" target="_blank"><?php echo Text::get('regular-twitter') ?></a></li>
                    <li class="facebook"><a href="http://facebook.com" target="_blank"><?php echo Text::get('regular-facebook') ?></a></li>
                    <li class="identica"><a href="http://identi.ca"><?php echo Text::get('regular-identica') ?></a></li>
                    <li class="gplus"><a href="http://plus.google.com"><?php echo Text::get('regular-google') ?></a></li>
                    <li class="rss"><a href="/rss">RSS</a></li>
                    
                </ul>
            </div>

           <div class="block sponsors">
                <h8 class="title"><?php echo Text::get('footer-header-sponsors') ?></h8>
<!--                <img id="current" src="../image/logo1-carusel.jpg" width="150" height="85"> -->
                
                
                <!-- para carrusel aplicar la misma solucion que para los banners 
                <?php foreach ($sponsors as $sponsor) : ?>
                <div class="sponsor">
                	<a href="<?php echo $sponsor->url ?>" title="<?php echo $sponsor->name ?>" target="_blank"><img src="/image/<?php echo $sponsor->image ?>/150/50" alt="<?php echo $sponsor->name ?>" /></a>
                </div>
                <?php break; endforeach; ?>
                por ahora maquetar un solo sponsor, para el carrusel se repetirá la maquetacion de este -->
            </div>

            <div class="block services" style="border-right:#ebe9ea 2px solid;">
                
                <h8 class="title"><?php echo Text::get('footer-header-services') ?></h8>
                <ul>
                    <li><a href="/service/campaign"><?php echo Text::get('footer-service-campaign') ?></a></li>
                    <li><a href="/service/workshop"><?php echo Text::get('footer-service-workshop') ?></a></li>
                    <li><a href="/service/consulting"><?php echo Text::get('footer-service-consulting') ?></a></li>
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
                    <li><a href="/blog"><?php echo Text::get('regular-header-blog'); ?></a></li>
                    <li><a href="/about/legal"><?php echo Text::get('regular-footer-legal'); ?></a></li>
                </ul>
    
                <div class="platoniq">
                   <span class="text"><?php echo Text::get('footer-platoniq-iniciative') ?></span> <span><a href="http://platoniq.net" target="_blank">Platoniq</a></span>
                </div>
    
       
        </div>

    </div>