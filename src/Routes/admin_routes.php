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

$admin = new RouteCollection();

////// ADMIN //////
$admin->add('admin-module-routing', new Route(
    '/{id}/{uri}',
    array('_controller' => 'Goteo\Controller\AdminController::routingAction',
        'uri' => ''
    ),
    array('uri' => '.*') // Matches any subroute
));

// $admin->add('admin-submodule-index', new Route(
//     '/{id}/{options}',
//     array('_controller' => 'Goteo\Controller\AdminController::submoduleAction',
//         'action' => 'list',
//         'id' => null,
//         'subaction' => null
//         )
// ));

////// OLD ROUTES //////
// $admin->add('admin-action', new Route(
//     '/{option}/{action}/{id}/{subaction}',
//     array('_controller' => 'Goteo\Controller\AdminController::optionAction',
//         'action' => 'list',
//         'id' => null,
//         'subaction' => null
//         )
// ));

return $admin;
