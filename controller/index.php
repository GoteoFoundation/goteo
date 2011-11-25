<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model\Project,
        Goteo\Model\Banner,
        Goteo\Model\Post,
        Goteo\Model\Promote,
        Goteo\Library\Text;

    class Index extends \Goteo\Core\Controller {
        
        public function index () {

            if (isset($_GET['error'])) {
                throw new \Goteo\Core\Error('418', Text::html('fatal-error-teapot'));
            }

            // hay que sacar los que van en portada de su blog (en cuanto aclaremos lo de los nodos)
            $posts    = Post::getList();
            $promotes = Promote::getAll(true);
            $banners  = Banner::getAll();

            foreach ($posts as $id=>$title) {
                $posts[$id] = Post::get($id);
            }

                foreach ($promotes as $key => &$promo) {
                    try {
                        $promo->projectData = Project::get($promo->project, LANG);
                    } catch (\Goteo\Core\Error $e) {
                        unset($promotes[$key]);
                    }
                }

                foreach ($banners as $id => &$banner) {
                    try {
                        $banner->project = Project::get($banner->project, LANG);
                    } catch (\Goteo\Core\Error $e) {
                        unset($banners[$id]);
                    }
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