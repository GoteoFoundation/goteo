<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\View,
        Goteo\Model;

    class Community extends \Goteo\Core\Controller {

        public function index () {

            $page = Page::get('community');
            $items = array();

            return new View(
                'view/community.html.php',
                array(
                    'name' => $page->name,
                    'title' => $page->description,
                    'content' => $page->content,
                    'items' => $items
                )
             );

        }

    }

}