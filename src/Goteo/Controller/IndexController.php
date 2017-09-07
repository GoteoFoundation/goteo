<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;

use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Model;
use Goteo\Model\Home;
use Goteo\Model\Banner;
use Goteo\Model\Stories;
use Goteo\Model\News;
use Goteo\Model\Post;
use Goteo\Model\Promote;
use Goteo\Library\Feed;

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

        View::setTheme('responsive');

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

            if ( ! $new->press_banner || ! $new->image instanceof \Goteo\Model\Image ) {
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
                'feed'      => $feed,
                'news'      => $news,
                'order'     => $order
            );


        return new Response(View::render('home/home', $vars));
    }

}
