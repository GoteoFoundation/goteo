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
        // Cache & replica read activated in this controller
        \Goteo\Core\DB::cache(true);
        \Goteo\Core\DB::replica(true);
        }

        public function index () {

            $page = Page::get('news');
            $news = Model\News::getAll();

            //Parse Content ONLY if data found on db
            if ($page) {
               $content = $page->parseContent();
            }

            return new View(
                'news.html.php',
                array(
                    'name' => $page->name,
                    'title' => $page->description,
                    'content' => $content,
                    'news' => $news
                )
             );

        }

    }

}
