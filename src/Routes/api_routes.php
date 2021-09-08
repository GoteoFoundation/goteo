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

// User keepalive compatibility service
$api->add('api-keepalive', new Route(
    '/keepalive',
    array('_controller' => 'Goteo\Controller\Api\UsersApiController::keepAliveAction'
        )
));

// GeoIP location service
$api->add('api-geoloc-ip', new Route(
    '/geoloc/ip',
    array('_controller' => 'Goteo\Controller\Api\GeolocApiController::geolocationAction'
        )
));

// Geolocate service for models
$api->add('api-geoloc-locate', new Route(
    '/geoloc/locate/{type}/{id}',
    array('_controller' => 'Goteo\Controller\Api\GeolocApiController::geolocateAction',
        'type' => '',
        'id' => ''
    )
));



// Blog
$api->add('api-blog-posts', new Route(
    '/blog/posts',
    array('_controller' => 'Goteo\Controller\Api\BlogApiController::postsAction',
        )
));

// Tags list
$api->add('api-blog-tags', new Route(
    '/blog/tags',
    array('_controller' => 'Goteo\Controller\Api\BlogApiController::tagsAction',
        )
));

// Post images upload (POST method only)
$api->add('api-blog-images-upload', new Route(
    '/blog/images',
    array('_controller' => 'Goteo\Controller\Api\BlogApiController::uploadImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

// Post images default (PUT method only)
$api->add('api-blog-images-default', new Route(
    '/blog/{id}/images/{image}',
    array('_controller' => 'Goteo\Controller\Api\BlogApiController::blogDefaultImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('PUT') // methods
));

// Post images delete (DELETE method only)
$api->add('api-blog-images-delete', new Route(
    '/blog/{id}/images/{image}',
    array('_controller' => 'Goteo\Controller\Api\BlogApiController::blogDeleteImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('DELETE') // methods
));
// Post property individual updates
$api->add('api-blog-post-property', new Route(
    '/blog/{id}/property/{prop}',
    array('_controller' => 'Goteo\Controller\Api\BlogApiController::postPropertyAction'
        )
));

$api->add('api-stories', new Route(
    '/stories',
    array('_controller' => 'Goteo\Controller\Api\StoriesApiController::storiesAction',
        )
));

// Stories property individual updates
$api->add('api-stories-property', new Route(
    '/stories/{id}/property/{prop}',
    array('_controller' => 'Goteo\Controller\Api\StoriesApiController::storiesPropertyAction'
        )
));
// Stories sort up/down arbitrarily (use the PUT method to sort)
$api->add('api-stories-sort', new Route(
    '/stories/{id}/sort',
    array('_controller' => 'Goteo\Controller\Api\StoriesApiController::storiesSortAction'
        )
));
// Stories images upload (POST method only)
$api->add('api-stories-images-upload', new Route(
    '/stories/images',
    array('_controller' => 'Goteo\Controller\Api\StoriesApiController::uploadImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

// Node Stories
// Stories sort up/down arbitrarily (use the PUT method to sort)
$api->add('api-channelstory-sort', new Route(
    '/channelstories/{node_id}/{stories_id}/sort',
    array('_controller' => 'Goteo\Controller\Api\ChannelStoriesApiController::nodestoriesSortAction'
        )
));

// Node Posts
// Post sort up/down arbitrarily (use the PUT method to sort)
$api->add('api-channelpost-sort', new Route(
    '/channelposts/{node_id}/{post_id}/sort',
    array('_controller' => 'Goteo\Controller\Api\ChannelPostsApiController::channelpostsSortAction'
        )
));

//Promote
// Add project to promote
$api->add('api-promote-add', new Route(
    '/promote/add',
    array('_controller' => 'Goteo\Controller\Api\PromoteApiController::promoteAddAction')
));

// Promote sort up/down (use the PUT method to sort)
$api->add('api-promote-sort', new Route(
    '/promote/id/{id}/sort',
    array('_controller' => 'Goteo\Controller\Api\PromoteApiController::promoteSortAction')
));

// Promote change property of active
$api->add('api-promote-property', new Route(
    '/promote/{id}/property/{prop}',
    array('_controller' => 'Goteo\Controller\Api\PromoteApiController::promotePropertyAction')
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

// Projects list
$api->add('api-ods-suggestion', new Route(
    '/social-commitment/ods-suggestion',
    array('_controller' => 'Goteo\Controller\Dashboard\AjaxDashboardController::odsSuggestionAction',
        )
));

$api->add('api-invest-msg-delete', new Route(
    '/projects/invest-msg/{mid}',
    array('_controller' => 'Goteo\Controller\Api\ProjectsApiController::projectDeleteSupportMsgAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('DELETE') // methods
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

$api->add('api-channels-images', new Route(
    '/channels/images',
    array('_controller' => 'Goteo\Controller\Api\ChannelsApiController::uploadImagesAction')
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
    array('_controller' => 'Goteo\Controller\Api\CategoriesApiController::keywordsAction')
));

// Categories images upload (POST method only)
$api->add('api-categories-images-upload', new Route(
    '/categories/images',
    array('_controller' => 'Goteo\Controller\Api\CategoriesApiController::uploadImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

// Categories property individual updates
$api->add('api-categories-property', new Route(
    '/categories/{tab}/{id}/property/{prop}',
    array('_controller' => 'Goteo\Controller\Api\CategoriesApiController::categoriesPropertyAction'
        )
));

// Categories list (tab may be category, sphere, social_commitment, footprint, sdg)
$api->add('api-categories', new Route(
    '/categories/{tab}',
    array('_controller' => 'Goteo\Controller\Api\CategoriesApiController::categoriesAction',
        'tab' => 'category'
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

// Communication 

// Post images upload (POST method only)
$api->add('api-communication-images-upload', new Route(
    '/communication/images',
    array('_controller' => 'Goteo\Controller\Api\CommunicationApiController::uploadImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

// Get success of a communication
$api->add('api-communication-success', new Route(
    '/communication/{id}/success',
    array('_controller' => 'Goteo\Controller\Api\CommunicationApiController::successAction')
));


// Get update of a communication
$api->add('api-communication-mail-success', new Route(
    '/communication/{id}/mail/{mail}',
    array('_controller' => 'Goteo\Controller\Api\CommunicationApiController::mailStatusAction')
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

// Project Mailing (generated from Messages to more than 2 users)
$api->add('api-messages-project-mailing', new Route(
    '/projects/{pid}/mailing',
    array('_controller' => 'Goteo\Controller\Api\MessagesApiController::projectMailingAction',
        )
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

// Matcher images upload (POST method only)
$api->add('api-matchers-images-upload', new Route(
    '/matchers/images',
    array('_controller' => 'Goteo\Controller\Api\MatchersApiController::uploadImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

// Matcher detail
$api->add('api-matcher-item', new Route(
    '/matchers/{mid}',
    array('_controller' => 'Goteo\Controller\Api\MatchersApiController::matcherAction')
));

// Workshops images upload (POST method only)
$api->add('api-workshops-images-upload', new Route(
    '/workshops/images',
    array('_controller' => 'Goteo\Controller\Api\WorkshopsApiController::uploadImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

// Workshops images upload (POST method only)
$api->add('api-channel-resources-images-upload', new Route(
    '/channel-resources/images',
    array('_controller' => 'Goteo\Controller\Api\ChannelResourcesApiController::uploadImagesAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));
// User images upload (POST method only)
$api->add('api-questionnaire-documents-upload', new Route(
    '/questionnaire/documents',
    array('_controller' => 'Goteo\Controller\Api\QuestionnaireApiController::questionnaireUploadDocumentsAction'),
    array(), // requirements
    array(), // options
    '', // host
    array(), // schemes
    array('POST') // methods
));

//Sdg list based on  footprints
$api->add('api-sdg-footprint-list', new Route(
    '/sdg/footprint',
    array('_controller' => 'Goteo\Controller\Dashboard\AjaxDashboardController::sdgFootprintAction')
));

return $api;
