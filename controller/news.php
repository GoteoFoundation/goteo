<?php

namespace Goteo\Controller {

    use Goteo\Library\Page,
        Goteo\Core\View,
        Goteo\Model;

    class News extends \Goteo\Core\Controller {

        public function index () {

            $page = Page::get('news');
            $news = Model\News::getAll();

            return new View(
                'view/news.html.php',
                array(
                    'name' => $page->name,
                    'title' => $page->description,
                    'content' => $page->content,
                    'news' => $news
                )
             );

        }

    }

}