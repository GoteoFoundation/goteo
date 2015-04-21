<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;

use Goteo\Application\View;
use Goteo\Model;
use Goteo\Model\Node;
use Goteo\Model\Home;
use Goteo\Model\Project;
use Goteo\Model\Banner;
use Goteo\Model\Stories;
use Goteo\Model\News;
use Goteo\Model\Post;
use // esto son entradas en portada o en footer
    Goteo\Model\Promote;
use Goteo\Model\Patron;
use Goteo\Model\Campaign;
use // convocatorias en portada
    Goteo\Model\User;
use Goteo\Model\Icon;
use Goteo\Model\Category;
use Goteo\Library\Text;
use Goteo\Library\Feed;
use Goteo\Library\Page;


// para sacar el contenido de about

class Index extends \Goteo\Core\Controller
{

    public function __construct()
    {
        //activamos la cache para todo el controlador index
        \Goteo\Core\DB::cache(true);
    }

    public function index()
    {

        if (isset($_GET['error'])) {
            throw new \Goteo\Core\Error('418', Text::html('fatal-error-teapot'));
        }

        // orden de los elementos en portada
        $order = Home::getAll();

        // si estamos en easy mode, quitamos el feed
        if (defined('GOTEO_EASY') && \GOTEO_EASY === true && isset($order['feed'])) {
            unset($order['feed']);
        }

        // entradas de blog
        if (isset($order['posts'])) {
            // entradas en portada
            $posts     = Post::getAll();
        }

        // Proyectos destacados
        if (isset($order['promotes'])) {
            $promotes  = Promote::getAll(true);
        }

        // capital riego
        if (isset($order['drops'])) {
            $calls     = Model\Call::getActive(3); // convocatorias en modalidad 1; inscripcion de proyectos
            $campaigns = Model\Call::getActive(4); // convocatorias en modalidad 2; repartiendo capital riego

            $drops = (!empty($calls) || !empty($campaigns)) ? true : false;
        }

        // padrinos
        if (isset($order['patrons'])) {
            $patrons  = Patron::getInHome();
        }
        // actividad reciente
        if (isset($order['feed'])) {
            $feed = array();

            $feed['goteo']     = Feed::getAll('goteo', 'public', 15);
            $feed['projects']  = Feed::getAll('projects', 'public', 15);
            $feed['community'] = Feed::getAll('community', 'public', 15);
        }

        $stories = (isset($order['stories'])) ? Stories::getAll(true) : array();

        $news =  (isset($order['news'])) ? News::getAll(true) : array();

        foreach ($news as $idNew => &$new) {
            //comprobamos si esta activo el campo banner prensa y si tiene imagen asociada

            if ((!$new->press_banner)||(!$new->image instanceof \Goteo\Model\Image)) {
                    unset($news[$idNew]);
            }

        }

        // Banners siempre
        $banners   = Banner::getAll(true);
        $vars = array(
                'banners'   => $banners,
                'stories'   => $stories,
                'posts'     => $posts,
                'promotes'  => $promotes,
                'calls'     => $calls,
                'campaigns' => $campaigns,
                'feed'      => $feed,
                'drops'     => $drops,
                'patrons'   => $patrons,
                'news'      => $news,
                'order'     => $order
            );


        return new Response(View::render('home/index', $vars));
    }

    public function indexNode() {
        return self::node_index();
    }

    public static function node_index($page = 'index')
    {

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

        return new Response(View::render(
            'home/index',
            array(
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

            )
        ));

    }
}
