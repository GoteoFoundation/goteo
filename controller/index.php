<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model\Post;

    class Index extends \Goteo\Core\Controller {
        
        public function index () {

            $posts = Post::getAll();

            return new View('view/index.html.php', array('posts' => $posts));
            
        }
        
    }
    
}