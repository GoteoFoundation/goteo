<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;

use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Model;
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

class IndexController extends \Goteo\Core\Controller
{

    public function __construct()
    {
        //activamos la cache para todo el controlador index
        \Goteo\Core\DB::cache(true);
    }

    public function indexAction()
    {

        if (isset($_GET['error'])) {
            throw new \Goteo\Core\Error('418', Text::html('fatal-error-teapot'));
        }

        // orden de los elementos en portada
        $order = Home::getAll(Config::get('node'));

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

}
