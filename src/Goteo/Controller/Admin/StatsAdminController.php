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
use Goteo\Payment\Payment;

class StatsAdminController extends AbstractAdminController {
    protected static $icon = '<i class="fa fa-2x fa-bar-chart"></i>';

    public static function getRoutes() {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::indexAction"]
            ),
            new Route(
                '/{sub}/{part}',
                ['_controller' => __CLASS__ . "::subAction",
                'part' => ''
                ]
            )
        ];
    }

    // public static function getGroup() {
    //     return 'stats';
    // }

    public static function getSidebar() {
        return [
            '/stats' => Text::get('admin-summary'),
            '/stats/totals/projects' => Text::get('admin-stats-project-totals'),
            '/stats/totals/invests' => Text::get('admin-stats-invest-totals'),
            '/stats/timeline' => Text::get('admin-aggregate-timeline'),
            '/stats/origins' => Text::get('admin-origins'),
        ];
    }

    public function indexAction(Request $request) {
        return $this->subAction('index', '', $request);
    }

    public function subAction($sub, $part = '', Request $request) {
        $template = "admin/stats/$sub";
        if($part) $template .= "/$part";
        elseif($sub === 'totals') {
            // Redirect to project totals
            return $this->redirect('/admin/stats/totals/projects');
        }
        if(!$this->getViewEngine()->find($template)) {
            throw new ControllerException("Template [$template] not found");
        }

        $filters = [];
        if($request->query->has('from')) $filters['from'] = $request->query->get('from');
        if($request->query->has('to')) $filters['to'] = $request->query->get('to');

        $methods = array_map(function($m) {
                return $m->getName();
            }, Payment::getMethods(Session::getUser()));

        $text = '';
        // Search engines
        $engines = ['channel', 'call', 'matcher', 'project', 'user', 'consultant'];
        foreach($engines as $e) {
            if($request->query->has($e)) {
                $text = $request->query->get($e);
                $filters[$e] = $text;
                break;
            }
        }
        // Nice name if available
        if($request->query->has('text')) $text = $request->query->get('text');

        return $this->viewResponse($template, [
            'engines' => $engines,
            'filters' => $filters,
            'text' => $text,
            'sub' => $sub,
            'part' => $part,
            'methods' => ['global' => Text::get('regular-all') ] + $methods,
            'intervals' => ['today' => 'the_day', 'week' => 'the_week', 'month' => 'the_month', 'year' => 'the_year']
        ]);
    }


}
