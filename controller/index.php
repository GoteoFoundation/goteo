<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model\Project,
        Goteo\Model\Banner,
        Goteo\Model\Post,
        Goteo\Model\Promote;

    class Index extends \Goteo\Core\Controller {
        
        public function index ($project = null) {

            if (isset($project)) {
                die('Llega ' . $project);
            }

            // hay que sacar los que van en portada de su blog (en cuanto aclaremos lo de los nodos)
            $posts    = Post::getList();
            $promotes = Promote::getAll();
            $banners  = Banner::getAll();

            foreach ($posts as $id=>$title) {
                $posts[$id] = Post::get($id);
            }

            foreach ($promotes as $key => &$promo) {
                $promo->projectData = Project::get($promo->project);
            }

            foreach ($banners as $id => &$banner) {
                $banner->project = Project::get($banner->project);
            }

            $post = isset($_GET['post']) ? $_GET['post'] : reset($posts)->id;

            return new View('view/index.html.php',
                array(
                    'banners'  => $banners,
                    'posts'    => $posts,
                    'promotes' => $promotes
                )
            );
            
        }
        
    }
    
}