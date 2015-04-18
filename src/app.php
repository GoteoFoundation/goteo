<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel;
use Goteo\Application\View;
use Goteo\Plates\Extension;

// views function registering
View::getEngine()->loadExtension(new Extension\TextUtils());
View::getEngine()->loadExtension(new Extension\Pages());

// Some defaults
View::getEngine()->addData([
    'title' => GOTEO_META_TITLE,
    'meta_description' => GOTEO_META_DESCRIPTION,
    'meta_keywords' => GOTEO_META_KEYWORDS,
    'meta_author' => GOTEO_META_AUTHOR,
    'meta_copyright' => GOTEO_META_COPYRIGHT,
    'url' => SITE_URL,
    'image' => SRC_URL . '/goteo_logo.png',
    // 'og_title' => 'Goteo.org',
    // 'og_description' => GOTEO_META_DESCRIPTION,
    ]);

$routes = new RouteCollection();
$routes->add('home', new Route(
    '/',
    array('_controller' => 'Goteo\Controller\Index::index')
));

return $routes;
