<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View;

    class About extends \Goteo\Core\Controller {
        
        public function index ($id = null) {

            if (empty($id)) {
                $id = 'about';
            }

            if ($id == 'faq') {
                throw new Redirection('/faq', Redirection::TEMPORARY);
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