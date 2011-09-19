<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Library\Text,
        Goteo\Library\Mail,
        Goteo\Library\Template;

    class About extends \Goteo\Core\Controller {
        
        public function index ($id = null) {

            if (empty($id)) {
                $id = 'about';

                $posts = Model\Info::getAll(true, \GOTEO_NODE);

                return new View(
                    'view/about/info.html.php',
                    array(
                        'posts' => $posts
                    )
                 );
            }

            if ($id == 'faq' || $id == 'contact') {
                throw new Redirection('/'.$id, Redirection::TEMPORARY);
            }

            $page = Page::get($id);

            if ($id == 'howto') {
                return new View(
                    'view/about/howto.html.php',
                    array(
                        'name' => $page->name,
                        'description' => $page->description,
                        'content' => $page->content
                    )
                 );
            }

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