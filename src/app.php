<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel;
use Goteo\Application\View;
use Goteo\Plates\Extension;

// LEGACY VIEWS
\Goteo\Core\View::addViewPath(GOTEO_WEB_PATH . 'view');
//NormalForm views
\Goteo\Core\View::addViewPath(GOTEO_PATH . 'src/Goteo/Library/NormalForm/view');
//SuperForm views
\Goteo\Core\View::addViewPath(GOTEO_PATH . 'src/Goteo/Library/SuperForm/view');
//TODO: PROVISIONAL
//add view
\Goteo\Core\View::addViewPath(GOTEO_WEB_PATH . 'nodesys');


// PLATES VIEWS
//Cache dir in libs
\Goteo\Library\Cacher::setCacheDir(GOTEO_CACHE_PATH);
//Default views
//General views
View::factory(GOTEO_WEB_PATH . 'templates/main'); //system fallback
//new templates
View::addFolder('main',  GOTEO_WEB_PATH . 'templates/main', true);
View::addFolder('node',  GOTEO_WEB_PATH . 'templates/node', true);
View::addFolder('call',  GOTEO_WEB_PATH . 'templates/call', true);

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
    'theme' => 'main'
    // 'og_title' => 'Goteo.org',
    // 'og_description' => GOTEO_META_DESCRIPTION,
    ]);

$routes = new RouteCollection();
$routes->add('home', new Route(
    '/',
    array('_controller' => 'Goteo\Controller\Index::index')
));

return $routes;
