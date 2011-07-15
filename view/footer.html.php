<?php

use Goteo\Library\Text,
    Goteo\Model\Category,
    Goteo\Model\Post,
    Goteo\Model\Sponsor;

$categories = Category::getList();  // categorias que se usan en proyectos
$posts      = Post::getList('footer');
$sponsors   = Sponsor::getList();
?>

    <div id="footer">

            <div class="categories">
                <h8 class="title">Categorías</h8>
                <ul>
                <?php foreach ($categories as $id=>$name) : ?>
                    <li><a href="/discover/results/<?php echo $id; ?>"><?php echo $name; ?></a></li>
                <?php endforeach; ?>
                </ul>
            </div>

            <div class="projects">
                <h8 class="title">Proyectos</h8>
                <ul>
                    <li><a href="/"><?php echo Text::get('home-promotes-header') ?></a></li>
                    <li><a href="/discover/view/popular"><?php echo Text::get('discover-group-popular-header') ?></a></li>
                    <li><a href="/discover/view/outdate"><?php echo Text::get('discover-group-outdate-header') ?></a></li>
                    <li><a href="/discover/view/recent"><?php echo Text::get('discover-group-recent-header') ?></a></li>
                    <li><a href="/discover/view/success"><?php echo Text::get('discover-group-success-header') ?></a></li>
                    <li><a href="/project/create"><?php echo Text::get('regular-create') ?></a></li>
                </ul>
            </div>

            <div class="resources">
                <h8 class="title">Recursos</h8>
                <ul>
                    <li><a href="/faq"><?php echo Text::get('regular-header-faq') ?></a></li>
                    <?php foreach ($posts as $id=>$title) : ?>
                    <li><a href="/blog/<?php echo $id ?>"><?php echo $title ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="social">
                <h8 class="title">Síganos</h8>
                <ul>
                    <li class="twitter"><a href="http://twitter.com" target="_blank"><?php echo Text::get('regular-twitter') ?></a></li>
                    <li class="facebook"><a href="http://facebook.com" target="_blank"><?php echo Text::get('regular-facebook') ?></a></li>
                    <li class="rss"><a href="/rss">RSS</a></li>
                </ul>
            </div>

            <div class="sponsors">
                <h8 class="title">Apoyos institucionales</h8>
                <!-- para carrusel aplicar la misma solucion que para los banners -->
                <?php foreach ($sponsors as $sponsor) : ?>
                <div class="sponsor"><a href="<?php echo $sponsor->url ?>" title="<?php echo $sponsor->name ?>" target="_blank"><img src="/image/<?php echo $sponsor->image ?>/150/50" alt="<?php echo $sponsor->name ?>" /></a></div>
                <!-- por ahora maquetar un solo sponsor, para el carrusel se repetirá la maquetacion de este -->
                <?php break; endforeach; ?>
            </div>

            <div class="services">
                <h8 class="title">Servicios</h8>
                <ul>
                    <li><a href="#">Campañas</a></li>
                    <li><a href="#">Talleres</a></li>
                    <li><a href="#">Consultoría</a></li>
                </ul>
            </div>

    </div>

    <div id="sub-footer">

        <div>
            
            <ul>
                <li><a href="/about"><?php echo Text::get('regular-header-about'); ?></a></li>
                <li><a href="/user/login"><?php echo Text::get('regular-login'); ?></a></li>
                <li><a href="/contact"><?php echo Text::get('regular-header-contact'); ?></a></li>
                <li><a href="/blog"><?php echo Text::get('regular-header-blog'); ?></a></li>
                <li><a href="/about/legal"><?php echo Text::get('regular-header-legal'); ?></a></li>
            </ul>

            <div class="platoniq">
                <p>Una iniciativa de: <strong><a href="http://platoniq.net" target="_blank">Platoniq</a></strong></p>
            </div>

        </div>

    </div>