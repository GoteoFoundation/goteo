<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$api = new RouteCollection();

// One Project chart preprocessed data (costs)
$api->add('api-chart-project-costs', new Route(
    '/projects/{id}/charts/costs',
    array('_controller' => 'Goteo\Controller\Api\ChartsApiController::projectCostsAction',
        )
));

// One Project chart preprocessed data (invests)
$api->add('api-chart-project-invests', new Route(
    '/projects/{id}/charts/invests',
    array('_controller' => 'Goteo\Controller\Api\ChartsApiController::projectInvestsAction',
        )
));

// One Project chart preprocessed data (origin)
$api->add('api-chart-project-referer', new Route(
    '/projects/{id}/charts/referer/{type}',
    array('_controller' => 'Goteo\Controller\Api\ChartsApiController::originStatsAction',
        'type' => 'project',
        'group' => 'referer'
        )
));

// One Project chart preprocessed data (origin)
$api->add('api-chart-project-device', new Route(
    '/projects/{id}/charts/device/{type}',
    array('_controller' => 'Goteo\Controller\Api\ChartsApiController::originStatsAction',
        'type' => 'project',
        'group' => 'ua'
        )
));

// any project/call origins stats
$api->add('api-charts-referers', new Route(
    '/charts/referers/{type}',
    array('_controller' => 'Goteo\Controller\Api\AdminChartsApiController::originStatsAction',
        'id' => null,
        'type' => 'project',
        'group' => 'referer'
        )
));

// any project/call origins stats (origin)
$api->add('api-chart-devices', new Route(
    '/charts/devices/{type}',
    array('_controller' => 'Goteo\Controller\Api\AdminChartsApiController::originStatsAction',
        'type' => 'project',
        'group' => 'ua'
        )
));

// aggregate time metric series
$api->add('api-chart-aggregates', new Route(
    '/charts/aggregates/{type}',
    array('_controller' => 'Goteo\Controller\Api\AdminChartsApiController::aggregatesAction',
        'type' => 'invests'
        )
));

// Totals for invests (today, yesterday, this week, etc)
$api->add('api-chart-totals-invests', new Route(
    '/charts/totals/invests/{target}/{method}/{slot}',
    array('_controller' => 'Goteo\Controller\Api\AdminChartsApiController::totalInvestsAction',
          'target' => '',
          'method' => 'global',
          'slot' => ''
        )
));

// Totals for projects
$api->add('api-chart-totals-projects', new Route(
    '/charts/totals/projects/{part}',
    array('_controller' => 'Goteo\Controller\Api\AdminChartsApiController::totalProjectsAction',
          'part' => null
        )
));

return $api;
