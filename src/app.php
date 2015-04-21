<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel;
use Goteo\Application\View;
use Goteo\Application\Config;
use Goteo\Foil\Extension;

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


//If node, Node templates first
//Node/call theme
if(Config::isNode()) {
    View::addFolder(GOTEO_PATH . 'extend/goteo/templates/node');
    View::addFolder(GOTEO_PATH . 'templates/node');
}

//Custom templates first (PROVISIONAL)
View::addFolder(GOTEO_PATH . 'extend/goteo/templates/default');

//Default templates
View::addFolder(GOTEO_PATH . 'templates/default');
// View::addFolder(GOTEO_PATH . 'templates/node', 'node');


// views function registering
// View::getEngine()->loadExtension(new Extension\GoteoCore());
View::getEngine()->loadExtension(new Extension\TextUtils(), [], true);
View::getEngine()->loadExtension(new Extension\Pages(), [], true);



// // Some defaults
View::getEngine()->useData([
    'title' => Config::getVar('title'),
    'meta_description' => Config::getVar('meta_description'),
    'meta_keywords' => Config::getVar('meta_keywords'),
    'meta_author' => Config::getVar('meta_author'),
    'meta_copyright' => Config::getVar('meta_copyright'),
    'URL' => SITE_URL,
    'SRC_URL' => SRC_URL,
    'image' => SRC_URL . '/goteo_logo.png'
    // 'og_title' => 'Goteo.org',
    // 'og_description' => GOTEO_META_DESCRIPTION,
    ]);

$routes = new RouteCollection();
$routes->add('home', new Route(
    '/',
    array('_controller' => 'Goteo\Controller\Index::' . (Config::isNode() ? 'indexNode' : 'index'))
));

$routes->add('discover', new Route(
    '/discover',
    array('_controller' => 'Goteo\Controller\Discover::index')
));
$routes->add('discover-patron', new Route(
    '/discover/patron',
    array('_controller' => 'Goteo\Controller\Discover::patron')
));
$routes->add('discover-calls', new Route(
    '/discover/calls',
    array('_controller' => 'Goteo\Controller\DiscoverAddons::calls')
));
$routes->add('discover-call', new Route(
    '/discover/call',
    array('_controller' => 'Goteo\Controller\DiscoverAddons::call')
));

return $routes;
