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
        Goteo\Core\Redirection,
        Goteo\Core\View;

    class Legal extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador legal
            \Goteo\Core\DB::cache(true);
        }

        public function index ($id = null) {

            if (empty($id)) {
                throw new Redirection('/about/legal', Redirection::PERMANENT);
            }

            $page = Page::get($id);

            return new View(
                'about/sample.html.php',
                array(
                    'name' => $page->name,
                    'description' => $page->description,
                    'content' => $page->parseContent()
                )
             );

        }

    }

}
