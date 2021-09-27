<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Goteo\Core\Controller;
use Goteo\Core\DB;
use Goteo\Core\View;
use Goteo\Model;
use Goteo\Model\Page;

class News extends Controller {

    public function __construct() {
        DB::cache(true);
        DB::replica(true);
    }

    public function index ()
    {
        $page = Page::get('news');
        $news = Model\News::getAll();

        if ($page) {
           $content = $page->parseContent();
        }

        return new View('news.html.php', [
            'name' => $page->name,
            'title' => $page->description,
            'content' => $content,
            'news' => $news
        ]);
    }

}
