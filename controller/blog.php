<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View;

    class Blog extends \Goteo\Core\Controller {
        
        public function index ($id = null) {

            return new View(
                'view/about/sample.html.php',
                array(
                    'name' => 'Blog',
                    'title' => 'Goteo blog',
                    'content' => 'BLOG'
                )
             );

        }
        
    }
    
}