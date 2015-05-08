<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Application\View,
        Goteo\Model,
        Goteo\Library\Text,
        Goteo\Library\Mail,
        Goteo\Library\Template,
        Symfony\Component\HttpFoundation\Response,
        Symfony\Component\HttpFoundation\RedirectResponse;

    class About extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador about
            \Goteo\Core\DB::cache(true);
        }

        public function index ($id = null) {

            // si llegan a la de mantenimiento sin estar en mantenimiento
            if ($id == 'maintenance' && GOTEO_MAINTENANCE !== true) {
                $id = 'credits';
            }

            // paginas especificas
            if ($id == 'faq' || $id == 'contact') {
                new RedirectResponse('/'.$id, Redirection::TEMPORARY);
            }

            // en estos casos se usa el contenido de goteo
            if ($id == 'howto' || $id == 'call') {
                if (!$_SESSION['user'] instanceof Model\User) {
                    new RedirectResponse('/');
                }
                $page = Page::get($id);
                /*return new View(
                    'about/howto.html.php',
                    array(
                        'name' => $page->name,
                        'description' => $page->description,
                        'content' => $page->content
                    )
                 );*/
                 return new Response(View::render('about/howto', array(
                        'name' => $page->name,
                        'description' => $page->description,
                        'content' => $page->content
                    )
                 ));

            }

            // el tipo de contenido de la pagina about es diferente
            if ( empty($id) ||
                 $id == 'about' ||
                ( NODE_ID != \GOTEO_NODE
                 && !\in_array($id, array('about', 'contact', 'press', 'service', 'maintenance', 'donations'))
                 )
                ) {
                $id = 'about';

                if (NODE_ID == \GOTEO_NODE) {
                    $posts = Model\Info::getAll(true, \GOTEO_NODE);

                    /*return new View(
                        'about/info.html.php',
                        array(
                            'posts' => $posts
                        )
                     );*/

                    return new Response(View::render('about/info', array(
                        'posts' => $posts
                    )
                 ));
                } else {

                    return \Goteo\Controller\Index::node_index('about');
/*
                    $page = Page::get($id, \NODE_ID);

                    return new View(
                        'node/about.html.php',
                        array(
                            'name' => $page->name,
                            'description' => $page->description,
                            'content' => $page->content
                        )
                     );
 *
 */
                }


            }

            // resto de casos
            $page = Page::get($id);

            /*return new View(
                'about/sample.html.php',
                array(
                    'name' => $page->name,
                    'description' => $page->description,
                    'content' => $page->content
                )
             );*/

            return new Response(View::render('about/sample', array(
                        'name' => $page->name,
                        'description' => $page->description,
                        'content' => $page->content
                    )
                 ));

        }

    }

}
