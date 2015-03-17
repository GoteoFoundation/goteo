<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\View,
        Goteo\Model;

    class Calendar extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador calendar
            \Goteo\Core\DB::cache(true);
        }

        public function index ($current = 'node') {

            return new View(
                'calendar.html.php',
                array(
                    'calendar'  => true
                )
             );

        }

    }

}
