<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\View;

    class About extends \Goteo\Core\Controller {
        
        public function index ($id = null) {

            if (empty($id)) {
                $id = 'about';
            }

            $page = Page::get($id);

//            $title = 'About' . (isset($title) ? ' / ' . ucfirst($title) : '');
            
            return new View(
                'view/about/sample.html.php',
                array(
                    'name' => $page->name,
                    'title' => $page->description,
                    'content' => $page->content
                )
             );

        }
        
    }
    
}