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

use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Model;
use Goteo\Model\Page;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use function in_array;

class AboutController extends Controller {

    public function __construct() {
        $this->dbReplica(true);
        $this->dbCache(true);

        View::setTheme('responsive');
    }

    public function indexAction ($id = ''): Response
    {

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

             return new Response(View::render('about/howto', array(
                    'name' => $page->name,
                    'description' => $page->description,
                    'content' => $page->parseContent()
                )
             ));
        }

        // el tipo de contenido de la pagina about es diferente
        if ( empty($id) || $id == 'about' ||
            (
                !Config::isMasterNode()
                && !in_array($id, array('about', 'contact', 'press', 'service', 'maintenance', 'donations'))
            )
        ) {
            $id = 'about';

            $posts = Model\Info::getAll(true, Config::get('node'));

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

    public function librejsAction(): Response
    {
        return $this->viewResponse('about/librejs');
    }

    public function legalAction(string $id = ''): Response
    {
        if (empty($id)) {
            return $this->redirect('/about/legal');
        }

        $page = Page::get($id);

        return $this->viewResponse('about/sample', array(
            'name' => $page->name,
            'description' => $page->description,
            'content' => $page->parseContent()
        ));
    }
}
