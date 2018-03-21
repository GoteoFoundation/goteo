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

use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\User;

class StatsAdminController extends AbstractAdminController {
    protected static $icon = '<i class="fa fa-2x fa-bar-chart"></i>';

    public static function getRoutes() {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::indexAction"]
            ),
            new Route(
                '/{zone}/{part}',
                ['_controller' => __CLASS__ . "::zoneAction",
                'part' => 'projects'
                ]
            )
        ];
    }

    public static function getGroup() {
        return 'main';
    }

    // public static function getSidebar() {
    //     return [
    //         '/users' => self::getLabel(),
    //         // '/users/stats' => Text::get('admin-stats')
    //     ];
    // }

    public function indexAction(Request $request) {
        return $this->zoneAction('index', '', $request);
    }

    public function zoneAction($zone, $part = 'projects', Request $request) {
        $template = "admin/stats/$zone";
        if($part) $template .= "/$part";
        if(!$this->getViewEngine()->find($template)) {
            throw new ControllerException("Template [$template] not found");
        }

        $filters = [
            'from' => $request->query->has('from') ? $request->query->get('from') : (new \Datetime('1 week'))->format('Y-m-d'),
            'to' => $request->query->has('to') ? $request->query->get('to') : null
        ];

        return $this->viewResponse($template, ['filters' => $filters, 'zone' => $zone, 'part' => $part]);
    }


}
