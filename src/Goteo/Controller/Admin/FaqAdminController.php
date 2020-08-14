<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

use Goteo\Model\Faq;
use Goteo\Application\App;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\View;
use Goteo\Library\Text;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ControllerAccessDeniedException;

class FaqAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-2x fa-question-circle-o"></i>';

    public static function getGroup() {
        return 'contents';
    }

    public static function getRoutes()
    {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/{section}',
                ['_controller' => __CLASS__ . "::listAction"]
            ),
            new Route(
                '/{section}/add',
                ['_controller' => __CLASS__ . "::addAction"]
            ),
            new Route(
                '/{section}/edit/{id}',
                ['_controller' => __CLASS__ . "::addAction"]
            )
        ];
    }

    public function listAction($section = null, Request $request)
    {
        if ($section) {
            $filters['subject'] = $section;
        }

        $limit = 25;
        $page = $request->query->get('pag') ?: 0;

        $sections = Faq::sections();
        $list = Faq::getList($filters, $page, $limit);
        $list = Faq::getList($filters, $page, $limit, true);

        // $list = Communication::getList($filters, $page * $limit, $limit, false, Config::get('lang'));
        // $total = Communication::getList($filters, 0, $limit, true, Config::get('lang'));
        return $this->viewResponse('admin/faq/list', [
            'list' => $list,
            'total' => $total,
            'limit' => $limit,
            'sections' => $sections
        ]);
        
    }   

    public function addAction($id = null, Request $request)
    {
    }

}
