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

use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FaqController extends \Goteo\Core\Controller {

    public function __construct() {
        // Cache & replica read activated in this controller
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
    }

    public function indexAction ($section='', $tag='', Request $request) {


        return $this->viewResponse('faq/index', [
                    /*'banners' => $banners,
                    'list_posts'   => $list_posts,
                    'blog_sections'     => $blog_sections,
                    'section'           => $section,
                    'tag'               => $tag,
                    'limit'             => $limit,
                    'total'             => $total*/
                ]
        );
    }

    public function sectionAction($section, Request $request)
    {

        return $this->viewResponse('faq/section', [
            
        ]);

    }

    public function individualAction($faq, Request $request)
    {

        return $this->viewResponse('faq/individual', [
            
        ]);

    }

}


