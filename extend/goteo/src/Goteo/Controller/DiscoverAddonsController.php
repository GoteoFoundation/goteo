<?php

namespace Goteo\Controller;


use Goteo\Application\View;
use Goteo\Model;
use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Library\Listing;

class DiscoverAddonsController extends \Goteo\Core\Controller {

    public function __construct() {
        //activamos la cache para todo el controlador index
        \Goteo\Core\DB::cache(true);
    }

    /*
     * Alias a mostrar todas las convocatorias
     */
    public function callAction () {
        return $this->redirect('/discover/calls');
    }

     /*
     * Ver todas las convocatorias
     */
    public function callsAction () {

        $viewData = array();

        // segun el tipo cargamos el título de la página
        $viewData['title'] = Text::html('discover-calls-header');

        // segun el tipo cargamos la lista
        $viewData['list']  = Model\Call::getActive(null, true);


        return $this->viewResponse('discover/calls', $viewData);

    }

}

