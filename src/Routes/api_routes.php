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

// Project images upload (POST method only)
$api->add('api-projects-images-upload', new Route(
    '/projects/{id}/images',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectUploadImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

// Project images default (PUT method only)
$api->add('api-projects-images-default', new Route(
    '/projects/{id}/images/{image}',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectDefaultImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('PUT') // methods
));

// Project images delete (DELETE method only)
$api->add('api-projects-images-delete', new Route(
    '/projects/{id}/images/{image}',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectDeleteImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('DELETE') // methods
));

// Project reorder images (POST method only)
$api->add('api-projects-images-reorder', new Route(
    '/projects/{id}/images/reorder',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectReorderImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

// Project materials
// Handles PUT (update element) and POST (new element) if required
$api->add('api-projects-materials', new Route(
    '/projects/{id}/materials',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectMaterialsAction'
        )
));

// Project property
$api->add('api-projects-property', new Route(
    '/projects/{id}/property/{prop}',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectPropertyAction'
        )
));

// Project updates property
$api->add('api-projects-updates-property', new Route(
    '/projects/{pid}/updates/{uid}/{prop}',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectUpdatesPropertyAction'
        )
));

// Licenses list
$api->add('api-licenses', new Route(
    '/licenses',
    array('_controller' => 'Goteo\Controller\Api\LicensesApiController::licensesAction',
        )
));

return $api;
