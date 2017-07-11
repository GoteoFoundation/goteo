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

// Users list
$api->add('api-users', new Route(
    '/users',
    array('_controller' => 'Goteo\Controller\Api\UsersApiController::usersAction',
        )
));

// User id availability checkpoint
$api->add('api-user-check', new Route(
    '/login/check',
    array('_controller' => 'Goteo\Controller\Api\UsersApiController::userCheckAction',
        )
));

// Projects list
$api->add('api-projects', new Route(
    '/projects',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectsAction',
        )
));

// One Project info
$api->add('api-project', new Route(
    '/projects/{id}',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectAction',
        )
));

// One Project chart preprocessed data (costs)
$api->add('api-chart-project', new Route(
    '/charts/{id}/costs',
    array('_controller' => 'Goteo\Controller\Api\ChartsApiController::projectCostsAction',
        )
));

// One Project chart preprocessed data (invests)
$api->add('api-chart-invests', new Route(
    '/charts/{id}/invests',
    array('_controller' => 'Goteo\Controller\Api\ChartsApiController::projectInvestsAction',
        )
));

return $api;
