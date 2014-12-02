<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text;

    class Service extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador service
            \Goteo\Core\DB::cache(true);
        }

        public function index ($id = null) {

            if (empty($id)) {
                $id = 'service';
            }

            $page = Page::get($id);

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
