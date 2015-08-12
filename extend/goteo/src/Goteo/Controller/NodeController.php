<?php

namespace Goteo\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Model;
use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Library\Listing;
use Goteo\Model\Node;
use Goteo\Model\Home;
use Goteo\Model\Icon;
use Goteo\Model\Category;
use Goteo\Model\Promote;
use Goteo\Model\Patron;
use Goteo\Model\Post;
use Goteo\Model\Project;
use Goteo\Library\Page;

class NodeController extends \Goteo\Core\Controller {

    public function __construct() {
        //activamos la cache para todo el controlador index
        \Goteo\Core\DB::cache(true);

    }

    /**
     * Any route node controller
     */
    public function subdomainAction ($url = '', Request $request) {
        $subdomain = strtok($request->getHost(), '.');
        if(!in_array($subdomain, array('barcelona', 'betabarcelona', 'www', 'beta', 'dev'))) {
            return $this->redirect('//goteo.org');
        }
        if(strpos($url, 'channel') === 0) {
            return $this->redirect('//goteo.org/' . $url);
        }

        // $folders = $this->getViewEngine()->finder()->dirs();
        // array_unshift($folders, __DIR__ . '/../../../templates/barcelona');
        // print_r($folders);
        // $this->getViewEngine()->setFolders($folders);
        // $folders = $this->getViewEngine()->finder()->dirs();
        // print_r($folders);die;

        $pages = array('' => 'index', 'about' => 'about');
        if(array_key_exists($url, $pages)) {
            //Get vars
            $vars = self::node_index($pages[$url]);
            // NEW SYSTEM:
            // return new Response(View::render( 'barcelona::index', $vars ));
            // OLD SYSTEM
            
            // Configuraciones específicas para nodos
            // Metadata
            define('NODE_META_TITLE', 'Goteo Barcelona - Cofinanciació del procomú');
            define('NODE_META_DESCRIPTION', 'Xarxa social de finançament col·lectiu');
            define('NODE_META_KEYWORDS', 'crowdfunding, procomún, commons, social, network, financiacion colectiva, cultural, creative commons, proyectos abiertos, open source, free software, licencias libres');
            define('NODE_META_AUTHOR', 'Fundación Goteo');
            define('NODE_META_COPYRIGHT', 'Platoniq');
            define('NODE_DEFAULT_LANG', 'ca');
            define('NODE_URL', 'http://barcelona.goteo.org');
            define('NODE_NAME', 'GoteoBarcelona');
            define('NODE_MAIL', 'barcelona@goteo.org');
            define('NODE_ANALYTICS_TRACKER', "<script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

              ga('create', 'UA-17744816-9', 'goteo.org');
              ga('send', 'pageview');

            </script>
            ");
            return new Response(\Goteo\Core\View::get( 'node/index.html.php', $vars ));
        }

        // die("$url");
        // Default: route to defaults
        // get routes
        $routes = App::getRoutes();
        // remove this route to avoid recursion
        $routes->remove('subdomain-node');
        //Return a sub-request
        $r = Request::create("/$url",
                             $request->getMethod(),
                             $request->getMethod() === 'GET' ? $request->query->all() : $request->request->all(),
                             $request->cookies->all(),
                             $request->files->all(),
                             $request->server->all()
                             );
        // var_dump($r);die;
        return App::get()->handle($r, HttpKernelInterface::SUB_REQUEST);
    }

    public static function node_index($page = 'index')
    {
        $node_id = Config::get('current_node');

        $node = Node::get($node_id);

        // orden de los elementos en portada
        $order = Home::getAll($node_id);
        $side_order = Home::getAll($node_id, 'side');

        $icons = Icon::getAll();
        $cats  = Category::getList();  // categorias que se usan en proyectos
        $hide_promotes = false;

        // Proyectos destacados primero para saber si lo meto en el buscador o no
        if (isset($order['promotes']) || isset($side_order['searcher'])) {
            $promotes  = Promote::getAll(true, $node_id);
        }

        // padrinos
        if (isset($order['patrons'])) {
            $patrons  =  $patrons = Patron::getInHome();
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
            $disc_popular = Project::published('popular', $node_id, 4);
            if (!empty($disc_popular)) {
                $searcher['popular'] = Text::get('node-side-searcher-popular');
                $discover['popular'] = $disc_popular;
            }

            $disc_recent = Project::published('recent', $node_id, 4);
            if (!empty($disc_recent)) {
                $searcher['recent'] = Text::get('node-side-searcher-recent');
                $discover['recent'] = $disc_recent;
            }

            $disc_success = Project::published('success', $node_id, 4);
            if (!empty($disc_success)) {
                $searcher['success'] = Text::get('node-side-searcher-success');
                $discover['success'] = $disc_success;
            }

            $disc_outdate = Project::published('outdate', $node_id, 4);
            if (!empty($disc_outdate)) {
                $searcher['outdate'] = Text::get('node-side-searcher-outdate');
                $discover['outdate'] = $disc_outdate;
            }

            $disc_byreward = array();
            foreach ($icons as $icon => $iconData) {
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
            foreach ($cats as $cat => $catName) {
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
            $sponsors = \Goteo\Model\Sponsor::getList($node_id);
        }

        // resto de centrales
        // entradas de blog
        if (isset($order['posts'])) {
            // entradas en portada
            $posts     = Post::getAll('home', $node_id);
        }

        // Convocatoris destacadas
        if (isset($order['calls'])) {
            $calls     = Call::getActive(3); // convocatorias en modalidad 1; inscripcion de proyectos
            $campaigns = Call::getActive(4); // convocatorias en modalidad 2; repartiendo capital riego
        }

        if ($page == 'about') {
            $pageData = Page::get($page, $node_id);
        } else {
            $pageData = null;
        }

        return array(
                'node'     => $node,
                'page'     => $pageData,

                // centrales
                'order'    => $order,
                    'posts'    => $posts,
                    'promotes' => $promotes,
                    'calls'    => array('calls'=>$calls, 'campaigns'=>$campaigns),
                    'patrons' => $patrons,

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

            );
    }

}

