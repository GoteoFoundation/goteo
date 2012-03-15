<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text;

    class Service extends \Goteo\Core\Controller {
        
        public function index ($id = null) {

            if (empty($id) || NODE_ID != \GOTEO_NODE) {
                $id = 'service';
            }

            $page = Page::get($id, \NODE_ID);

            return new View(
                'view/about/sample.html.php',
                array(
                    'name' => $page->name,
                    'description' => $page->description,
                    'content' => $page->content
                )
             );

        }
        
    }
    
}