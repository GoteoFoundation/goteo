<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Library\Mail;

    class Legal extends \Goteo\Core\Controller {
        
        public function index ($id = null) {

            if (empty($id)) {
                throw new Redirection('/about/legal', Redirection::PERMANENT);
            }

            $page = Page::get($id);

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