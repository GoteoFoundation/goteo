<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller {

    use Goteo\Model\Page,
        Goteo\Core\View,
        Goteo\Model;

    class News extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador news
            \Goteo\Core\DB::cache(true);
        }

        public function index () {

            $page = Page::get('news');
            $news = Model\News::getAll();

            return new View(
                'news.html.php',
                array(
                    'name' => $page->name,
                    'title' => $page->description,
                    'content' => $page->parseContent(),
                    'news' => $news
                )
             );

        }

    }

}
