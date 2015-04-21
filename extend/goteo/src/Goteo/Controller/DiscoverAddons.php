<?php

namespace Goteo\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Goteo\Application\View;
use Goteo\Model;
use Goteo\Library\Text;
use Goteo\Library\Message;
use Goteo\Library\Listing;

class DiscoverAddons extends \Goteo\Core\Controller {

    public function __construct() {
        //activamos la cache para todo el controlador index
        \Goteo\Core\DB::cache(true);
    }

    /*
     * Alias a mostrar todas las convocatorias
     */
    public function call () {
        return new RedirectResponse('/discover/calls');
    }

     /*
     * Ver todas las convocatorias
     */
    public function calls () {

        $viewData = array();

        // segun el tipo cargamos el título de la página
        $viewData['title'] = Text::html('discover-calls-header');

        // segun el tipo cargamos la lista
        $viewData['list']  = Model\Call::getActive(null, true);


        return new Response(View::render('discover/calls', $viewData));

    }

}

