<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel;
use Goteo\Application\View;
use Goteo\Application\Config;
use Goteo\Plates\Extension;

/**********************************/
// LEGACY VIEWS
\Goteo\Core\View::addViewPath(GOTEO_WEB_PATH . 'view');
//NormalForm views
\Goteo\Core\View::addViewPath(GOTEO_PATH . 'src/Goteo/Library/NormalForm/view');
//SuperForm views
\Goteo\Core\View::addViewPath(GOTEO_PATH . 'src/Goteo/Library/SuperForm/view');
//TODO: PROVISIONAL
//add view
\Goteo\Core\View::addViewPath(GOTEO_WEB_PATH . 'nodesys');
/**********************************/


//Node/call theme
if(Config::isNode()) {
    View::setTheme('node');
}

// PLATES VIEWS
//Cache dir in libs
\Goteo\Library\Cacher::setCacheDir(GOTEO_CACHE_PATH);
//Default views
//General views
View::factory(GOTEO_PATH . 'templates/main'); //system fallback
//new templates
View::addFolder('main',  GOTEO_PATH . 'templates/main', true);
View::addFolder('node',  GOTEO_PATH . 'templates/node', true);
View::addFolder('call',  GOTEO_PATH . 'templates/call', true);
//Custom templates

// views function registering
View::getEngine()->loadExtension(new Extension\GoteoCore());
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
    'theme' => View::getTheme()
    // 'og_title' => 'Goteo.org',
    // 'og_description' => GOTEO_META_DESCRIPTION,
    ]);

$routes = new RouteCollection();
$routes->add('home', new Route(
    '/',
    array('_controller' => 'Goteo\Controller\Index::' . (Config::isNode() ? 'indexNode' : 'index'))
));

return $routes;
