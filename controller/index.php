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
        Goteo\Library\Text,
        Goteo\Library\Feed;

    class Index extends \Goteo\Core\Controller {
        
        public function index () {

            if (NODE_ID != GOTEO_NODE) {
                return $this->node_index();
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

                foreach ($posts as $id=>$title) {
                    $posts[$id] = Post::get($id);
                }
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
            $banners   = Banner::getAll();

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

        public function node_index () {
            
            $node = Node::get(NODE_ID);

            // orden de los elementos en portada
            $side_order = Home::getAllSide(NODE_ID);

            // Laterales
            // ---------------------
            if (isset($side_order['searcher'])) {
                // Selector proyectos: los destacados, los grupos de discover y los retornos
                $searcher = array(
                    'promote' => Text::get('home-promotes-header'),
                    'popular' => Text::get('discover-group-popular-header'),
                    'recent'  => Text::get('discover-group-recent-header'),
                    'success' => Text::get('discover-group-success-header'),
                    'outdate' => Text::get('discover-group-outdate-header'),
                    'byreward' => Text::get('discover-searcher-byreward-header')
                );
            }

            if (isset($side_order['summary'])) {
                // Resumen proyectos: total proyectos, activos (en campaña), exitosos (que han llegado al mínimo), cofinanciadores (diferentes), colaboradores (diferentes) y total de dinero recaudado
                $summary = array(
                    'projects' => 250,
                    'active' => 16,
                    'success' => 9,
                    'investors' => 2376,
                    'supporters' => 53,
                    'amount' => 120000
                );
            }

            if (isset($side_order['sumcalls'])) {
                // Resumen convocatorias: nº campañas abiertas, nº convocatorias activas, importe total de las campañas, resto total
                $sumcalls = array(
                    'budget' => 16000,
                    'rest' => 11860,
                    'calls' => 15,
                    'campaigns' => 223
                );
            }

            if (isset($side_order['sponsors'])) {
                // Patrocinadores del nodo
                $sponsors = \Goteo\Model\Sponsor::getList(NODE_ID);
            }


            // Centrales
            // --------------------------
            $order = Home::getAll(NODE_ID);

            // entradas de blog
            if (isset($order['posts'])) {
                // entradas en portada
                $posts     = Post::getList('home', NODE_ID);

                foreach ($posts as $id=>$title) {
                    $posts[$id] = Post::get($id);
                }
            }

            // Proyectos destacados
            if (isset($order['promotes'])) {
                $promotes  = Promote::getAll(true, NODE_ID);

                foreach ($promotes as $key => &$promo) {
                    try {
                        $promo->projectData = Project::getMedium($promo->project, LANG);
                    } catch (\Goteo\Core\Error $e) {
                        unset($promotes[$key]);
                    }
                }
            }

            // Convocatoris destacadas
            if (isset($order['calls'])) {
                $calls  = Campaign::getAll(true, NODE_ID);

                foreach ($calls as $key => &$call) {
                    try {
                        $call = Call::get($call->call);
                    } catch (\Goteo\Core\Error $e) {
                        unset($calls[$key]);
                    }
                }
            }

            return new View('view/node/index.html.php',
                array(
                    'node'     => $node,

                    // centrales
                    'order'    => $order,
                        'posts'    => $posts,
                        'promotes' => $promotes,
                        'calls'    => $calls,
                    
                    // laterales
                    'side_order' => $side_order,
                        'searcher' => $searcher,
                        'summary'  => $summary,
                        'sumcalls' => $sumcalls,
                        'sponsors' => $sponsors
                )
            );

        }

    }
    
}