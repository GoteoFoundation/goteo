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

// User images upload (POST method only)
$api->add('api-user-avatar-upload', new Route(
    '/users/{id}/avatar',
    array('_controller' => 'Goteo\Controller\Api\UsersApiController::userUploadAvatarAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

// User property
$api->add('api-user-property', new Route(
    '/users/{id}/property/{prop}',
    array('_controller' => 'Goteo\Controller\Api\UsersApiController::userPropertyAction'
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

// Project rewards invests fulfilled
$api->add('api-projects-invests-fulfilled', new Route(
    '/projects/{pid}/invests/{iid}/fulfilled',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectInvestsFulfilledAction'
        )
));

// Project CSV rewards invests
$api->add('api-projects-invests-csv', new Route(
    '/projects/{pid}/invests/csv',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectInvestsCSVAction'
        )
));


// Calls list
$api->add('api-calls', new Route(
    '/calls',
    array('_controller' => 'Goteo\Controller\Api\CallsApiController::callsAction',
        )
));

// Channels list
$api->add('api-channels', new Route(
    '/channels',
    array('_controller' => 'Goteo\Controller\Api\ChannelsApiController::channelsAction',
        )
));


// Licenses list
$api->add('api-licenses', new Route(
    '/licenses',
    array('_controller' => 'Goteo\Controller\Api\LicensesApiController::licensesAction',
        )
));

// Keywords list
$api->add('api-keywords', new Route(
    '/keywords',
    array('_controller' => 'Goteo\Controller\Api\CategoriesApiController::keywordsAction',
        )
));

// Messages list
$api->add('api-comments-project', new Route(
    '/projects/{pid}/comments',
    array('_controller' => 'Goteo\Controller\Api\MessagesApiController::commentsAction',
        )
));

$api->add('api-comments-add', new Route(
    '/comments',
    array('_controller' => 'Goteo\Controller\Api\MessagesApiController::addCommentAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

$api->add('api-comments-delete', new Route(
    '/comments/{cid}',
    array('_controller' => 'Goteo\Controller\Api\MessagesApiController::deleteCommentAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('DELETE') // methods
));

// Messages list
$api->add('api-messages-project', new Route(
    '/projects/{pid}/messages',
    array('_controller' => 'Goteo\Controller\Api\MessagesApiController::messagesAction',
        )
));
// User Messages list
$api->add('api-messages-project-user', new Route(
    '/projects/{pid}/messages/{uid}',
    array('_controller' => 'Goteo\Controller\Api\MessagesApiController::userMessagesAction',
        )
));

$api->add('api-messages-add', new Route(
    '/messages',
    array('_controller' => 'Goteo\Controller\Api\MessagesApiController::addMessageAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

$api->add('api-stats-investors-required', new Route(
    '/stats/investors-required',
    array('_controller' => 'Goteo\Controller\Api\StatsApiController::investorsRequiredAction')
));

// Matcher routes
$api->add('api-matcher-list', new Route(
    '/matchers',
    array('_controller' => 'Goteo\Controller\Api\MatchersApiController::matchersAction')
));

// Matcher detail
$api->add('api-matcher-item', new Route(
    '/matchers/{mid}',
    array('_controller' => 'Goteo\Controller\Api\MatchersApiController::matcherAction')
));

return $api;
