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

            if ( empty($id) ||
                 $id == 'about' ||
                ( NODE_ID != \GOTEO_NODE
                 && !\in_array($id, array('about', 'contact', 'press', 'service'))
                 )
                ) {
                $id = 'about';

                if (NODE_ID == \GOTEO_NODE) {
                    $posts = Model\Info::getAll(true, \GOTEO_NODE);

                    return new View(
                        'view/about/info.html.php',
                        array(
                            'posts' => $posts
                        )
                     );
                } else {

                    $page = Page::get($id, \NODE_ID);

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

            if ($id == 'maintenance' && GOTEO_MAINTENANCE !== true) {
                $id = 'credits';
            }

            if ($id == 'faq' || $id == 'contact') {
                throw new Redirection('/'.$id, Redirection::TEMPORARY);
            }

            $page = Page::get($id, \NODE_ID);

            if ($id == 'howto' || $id == 'call') {
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