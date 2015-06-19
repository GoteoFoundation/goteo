<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Response;
use Goteo\Application\View;

class CalendarController extends \Goteo\Core\Controller {

    public function __construct() {
        //activamos la cache para todo el controlador calendar
        \Goteo\Core\DB::cache(true);
    }

    public function indexAction () {

        return new Response(
                View::render('calendar', [
                        'calendar'  => true
                    ])
            );
    }

}

