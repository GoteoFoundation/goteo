<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text;

    class Press extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador press
            \Goteo\Core\DB::cache(true);
        }

        public function index () {

            $page = Page::get('press', \NODE_ID);

            return new View(
                'about/sample.html.php',
                array(
                    'name' => $page->name,
                    'description' => $page->description,
                    'content' => $page->content
                )
             );

        }

    }

}
