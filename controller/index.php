<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model\Node,
        Goteo\Model\Home,
        Goteo\Model\Project,
        Goteo\Model\Banner,
        Goteo\Model\Call,
        Goteo\Model\Post,  // esto son entradas en portada o en footer
        Goteo\Model\Promote,
        Goteo\Model\Patron,
        Goteo\Model\Campaign, // convocatorias en portada
        Goteo\Model\User,
        Goteo\Model\Icon,
        Goteo\Model\Category,
        Goteo\Library\Text,
        Goteo\Library\Feed,
        Goteo\Library\Page; // para sacar el contenido de about

    class Index extends \Goteo\Core\Controller {
        
        public function index () {

            if (NODE_ID != GOTEO_NODE) {
                return self::node_index();
            }

            if (isset($_GET['error'])) {
                throw new \Goteo\Core\Error('418', Text::html('fatal-error-teapot'));
            }

            // orden de los elementos en portada
            $order = Home::getAll();

            // entradas de blog
            if (isset($order['posts'])) {
                // entradas en portada
                $posts     = Post::getAll();
            }

            // Proyectos destacados
            if (isset($order['promotes'])) {
                $promotes  = Promote::getAll(true);

                foreach ($promotes as $key => &$promo) {
                    try {
                        $promo->projectData = Project::getMedium($promo->project, LANG);
                    } catch (\Goteo\Core\Error $e) {
                        unset($promotes[$key]);
                    }
                }
            }

            // capital riego
            if (isset($order['drops'])) {
                $calls     = Call::getActive(3); // convocatorias en modalidad 1; inscripcion de proyectos
                $campaigns = Call::getActive(4); // convocatorias en modalidad 2; repartiendo capital riego
                
                $drops = (!empty($calls) || !empty($campaigns)) ? true : false;
            }

            // padrinos
            if (isset($order['patrons'])) {
                $patrons  =  Patron::getActiveVips();

                foreach ($patrons as $userId => $user) {
                    try {
                        $userData = User::getMini($userId);
                        $vipData = User\Vip::get($userId);
                        if (!empty($vipData->image)) {
                            $userData->avatar = $vipData->image;
                        }
                        $patrons[$userId] = $userData;
                    } catch (\Goteo\Core\Error $e) {
                        unset($patrons[$key]);
                    }
                }

            }

            // actividad reciente
            if (isset($order['feed'])) {
                $feed = array();

                $feed['goteo']     = Feed::getAll('goteo', 'public', 15);
                $feed['projects']  = Feed::getAll('projects', 'public', 15);
                $feed['community'] = Feed::getAll('community', 'public', 15);
            }
            
            // Banners siempre
            $banners   = Banner::getAll(true);

            foreach ($banners as $id => &$banner) {
                try {
                    $banner->project = Project::get($banner->project, LANG);
                } catch (\Goteo\Core\Error $e) {
                    unset($banners[$id]);
                }
            }

            return new View('view/index.html.php',
                array(
                    'banners'   => $banners,
                    'posts'     => $posts,
                    'promotes'  => $promotes,
                    'calls'     => $calls,
                    'campaigns' => $campaigns,
                    'feed'      => $feed,
                    'drops'     => $drops,
                    'patrons'   => $patrons,
                    'order'     => $order
                )
            );
            
        }

        public static function node_index ($page = 'index') {

            $node = Node::get(NODE_ID);

            // orden de los elementos en portada
            $order = Home::getAll(NODE_ID);
            $side_order = Home::getAllSide(NODE_ID);

            $icons = Icon::getAll();
            $cats  = Category::getList();  // categorias que se usan en proyectos
            $hide_promotes = false;

            // Proyectos destacados primero para saber si lo meto en el buscador o no
            if (isset($order['promotes']) || isset($side_order['searcher'])) {
                $promotes  = Promote::getAll(true, NODE_ID);

                foreach ($promotes as $key => &$promo) {
                    try {
                        $promo->projectData = Project::getMedium($promo->project, LANG);
                    } catch (\Goteo\Core\Error $e) {
                        unset($promotes[$key]);
                    }
                }
            }

            // Laterales
            // ---------------------
            if (isset($side_order['searcher'])) {
                // Selector proyectos: los destacados, los grupos de discover y los retornos
                $searcher = array();
                $discover = array();

                if (!empty($promotes)) {
                    $searcher['promote'] = Text::get('node-side-searcher-promote');
                    if ($page == 'about') {
                        $hide_promotes = true;
                    }
                }

                // vamos sacando los 4 primeros de cada categoria (excepto promotes y excepto byreward)
                // si una categoria no tiene proyectos no la ponemos en los pastillos del buscador
                $disc_popular = Project::published('popular', 4);
                if (!empty($disc_popular)) {
                    $searcher['popular'] = Text::get('node-side-searcher-popular');
                    $discover['popular'] = $disc_popular;
                }

                $disc_recent = Project::published('recent', 4);
                if (!empty($disc_recent)) {
                    $searcher['recent'] = Text::get('node-side-searcher-recent');
                    $discover['recent'] = $disc_recent;
                }

                $disc_success = Project::published('success', 4);
                if (!empty($disc_success)) {
                    $searcher['success'] = Text::get('node-side-searcher-success');
                    $discover['success'] = $disc_success;
                }

                $disc_outdate = Project::published('outdate', 4);
                if (!empty($disc_outdate)) {
                    $searcher['outdate'] = Text::get('node-side-searcher-outdate');
                    $discover['outdate'] = $disc_outdate;
                }

                $disc_byreward = array();
                foreach ($icons as $icon=>$iconData) {
                    $icon_projs = \Goteo\Library\Search::params(array('reward'=>array("'$icon'"), 'node'=>true), false, 4);
                    if (!empty($icon_projs)) {
                        $disc_byreward[$icon] = $icon_projs;
                    }
                }
                if (!empty($disc_byreward)) {
                    $searcher['byreward'] = Text::get('node-side-searcher-byreward');
                    $discover['byreward'] = $disc_byreward;
                }

            }

            if (isset($side_order['categories'])) {
                // Proyectos por categorías
                $categories = array();
                foreach ($cats as $cat=>$catName) {
                    $cat_projs = \Goteo\Library\Search::params(array('category'=>array("'$cat'"), 'node'=>true), false);
                    if (!empty($cat_projs)) {
                        $categories[$cat]['name'] = $catName;
                        $categories[$cat]['projects'] = $cat_projs;
                    }
                }
            }

            if (isset($side_order['summary'])) {
                $summary = $node->getSummary();
            }

            if (isset($side_order['sumcalls'])) {
                $sumcalls = $node->getSumcalls();
            }

            if (isset($side_order['sponsors'])) {
                // Patrocinadores del nodo
                $sponsors = \Goteo\Model\Sponsor::getList(NODE_ID);
            }

            // resto de centrales
            // entradas de blog
            if (isset($order['posts'])) {
                // entradas en portada
                $posts     = Post::getAll('home', NODE_ID);
            }

            // Convocatoris destacadas
            if (isset($order['calls'])) {
                $calls     = Call::getActive(3); // convocatorias en modalidad 1; inscripcion de proyectos
                $campaigns = Call::getActive(4); // convocatorias en modalidad 2; repartiendo capital riego
            }

            if ($page == 'about') {
                $pageData = Page::get($page, \NODE_ID);
            } else {
                $pageData = null;
            }

            return new View('view/node/index.html.php',
                array(
                    'node'     => $node,
                    'page'     => $pageData,

                    // centrales
                    'order'    => $order,
                        'posts'    => $posts,
                        'promotes' => $promotes,
                        'calls'    => array('calls'=>$calls, 'campaigns'=>$campaigns),
                    
                    // laterales
                    'side_order' => $side_order,
                        'searcher' => $searcher,
                        'categories' => $categories,
                        'summary'  => $summary,
                        'sumcalls' => $sumcalls,
                        'sponsors' => $sponsors,

                    // iconos de recompensas
                    'icons' => $icons,

                    // si hay que ocultar los destacados (por ser el about)
                    'hide_promotes' => $hide_promotes,

                    // los ocultos del discover
                    'discover' => $discover

                )
            );

        }

    }
    
}