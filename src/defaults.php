<?php

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


//Compiled views by grunt
View::addFolder(GOTEO_WEB_PATH . 'templates/grunt', 'compiled');

//If node, Node templates first
//Node/call theme
if(Config::isNode()) {
    //Custom templates first (PROVISIONAL: should be configurable in settings)
    View::addFolder(GOTEO_PATH . 'extend/goteo/templates/node', 'node-goteo');
    //Nodes views
    View::addFolder(GOTEO_PATH . 'templates/node', 'node');
}

//Custom templates first (PROVISIONAL: should be configurable in settings)
View::addFolder(GOTEO_PATH . 'extend/goteo/templates/default', 'goteo');

//Default templates
View::addFolder(GOTEO_PATH . 'templates/default', 'default');

// print_r(View::getEngine());

// views function registering
View::getEngine()->loadExtension(new Extension\GoteoCore(), [], true);
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
