<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View;

    class Blog extends \Goteo\Core\Controller {
        
        public function index ($show = 'list', $post = null) {

            // muestra, segun show: list , post

            // segun eso montamos los viewData


            return new View(
                'view/blog/index.html.php',
                array(
                    'name' => 'Blog',
                    'title' => 'Goteo blog',
                    'content' => 'BLOG'
                )
             );

        }
        
    }
    
}