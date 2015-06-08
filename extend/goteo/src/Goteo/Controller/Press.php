<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Application\Config,
        Goteo\Library\Text;

    class Press extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador press
            \Goteo\Core\DB::cache(true);
        }

        public function index () {

            $page = Page::get('press', Config::get('current_node'));

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
