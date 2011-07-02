<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model\Project,
        Goteo\Model\Post,
        Goteo\Model\Promote;

    class Index extends \Goteo\Core\Controller {
        
        public function index () {

            // hay que sacar los que van en portada de su blog (en cuanto aclaremos lo de los nodos)
            $posts    = Post::getAll();
            $promotes = Promote::getAll();

            foreach ($promotes as $key => &$promo) {
                $promo->projectData = Project::get($promo->project);
            }

            $post = isset($_GET['post']) ? $_GET['post'] : reset($posts)->id;

            return new View('view/index.html.php',
                array(
                    'post' => $post,
                    'posts' => $posts,
                    'promotes' => $promotes
                )
            );
            
        }
        
    }
    
}