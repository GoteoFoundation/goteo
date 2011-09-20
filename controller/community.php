<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\View,
        Goteo\Model;

    class Community extends \Goteo\Core\Controller {

        public function index ($show = 'activity') {

            // si show = activity -> feed

            // si show = sharemates -> compartiendo intereses global


            $page = Page::get('community');
            $items = array();

            return new View(
                'view/community.html.php',
                array(
                    'name' => $page->name,
                    'description' => $page->description . ' : ' . $show,
                    'content' => $page->content,
                    'items' => $items
                )
             );

        }

    }

}