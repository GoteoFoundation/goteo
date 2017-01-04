<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller {

    use Goteo\Model\Page,
        Goteo\Application\Config,
        Goteo\Application\View,
        Goteo\Application\Session,
        Goteo\Model,
        Goteo\Library\Text,
        Goteo\Model\Template,
        Symfony\Component\HttpFoundation\Response,
        Symfony\Component\HttpFoundation\RedirectResponse;

    class AboutController extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador about
            \Goteo\Core\DB::cache(true);
        }

        public function indexAction ($id = '') {

            // si llegan a la de mantenimiento sin estar en mantenimiento
            if ($id == 'maintenance' && Config::get('maintenance') !== true) {
                $id = 'credits';
            }

            // paginas especificas
            if ($id == 'faq' || $id == 'contact') {
                return new RedirectResponse('/'.$id);
            }

            // en estos casos se usa el contenido de goteo
            if ($id == 'howto') {
                return new RedirectResponse('/project/create');
            }
            //TODO: delete this, do it on call controller
            if( $id == 'call') {
                if (!Session::isLogged()) {
                    return new RedirectResponse('/');
                }
                $page = Page::get($id);

                 return new Response(View::render('project/howto', array(
                        'name' => $page->name,
                        'description' => $page->description,
                        'content' => $page->parseContent()
                    )
                 ));

            }

            // el tipo de contenido de la pagina about es diferente
            if ( empty($id) ||
                 $id == 'about' ||
                ( !\Goteo\Application\Config::isMasterNode()
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

            return $this->viewResponse('about/sample', array(
                        'name' => $page->name,
                        'description' => $page->description,
                        'content' => $page->parseContent()
                    ));

        }

        public function librejsAction() {
            return $this->viewResponse('about/librejs');
        }
    }

}
