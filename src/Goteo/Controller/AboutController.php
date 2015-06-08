<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Application\View,
        Goteo\Application\Session,
        Goteo\Model,
        Goteo\Library\Text,
        Goteo\Library\Mail,
        Goteo\Library\Template,
        Symfony\Component\HttpFoundation\Response,
        Symfony\Component\HttpFoundation\RedirectResponse;

    class AboutController extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador about
            \Goteo\Core\DB::cache(true);
        }

        public function indexAction ($id = '') {

            // si llegan a la de mantenimiento sin estar en mantenimiento
            if ($id == 'maintenance' && GOTEO_MAINTENANCE !== true) {
                $id = 'credits';
            }

            // paginas especificas
            if ($id == 'faq' || $id == 'contact') {
                return new RedirectResponse('/'.$id);
            }

            // en estos casos se usa el contenido de goteo
            if ($id == 'howto' || $id == 'call') {
                if (!Session::isLogged()) {
                    return new RedirectResponse('/');
                }
                $page = Page::get($id);

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
                ( \Goteo\Application\Config::isNode()
                 && !\in_array($id, array('about', 'contact', 'press', 'service', 'maintenance', 'donations'))
                 )
                ) {
                $id = 'about';

                $posts = Model\Info::getAll(true, \Goteo\Application\Config::get('node'));

                return new Response(View::render('about/info', array(
                    'posts' => $posts
                )
             ));

            }

            // resto de casos
            $page = Page::get($id);

            return new Response(View::render('about/sample', array(
                        'name' => $page->name,
                        'description' => $page->description,
                        'content' => $page->content
                    )
                 ));

        }

    }

}
