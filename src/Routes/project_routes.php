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
use Symfony\Component\HttpFoundation\RedirectResponse;

$prjs = new RouteCollection();

//// PROJECT /////
/// TODO: more methods... ///

// Project edit (old route compatibility)
$prjs->add('project-edit', new Route(
    '/edit/{pid}/{step}',
    // array(
    //     '_controller' => 'Goteo\Controller\ProjectController::editAction',
    //     'step' => 'userProfile'
    //     )
    // redirects to dashboard editing
    array('_controller' => function ($pid, $step = null) {
        if($step == 'userProfile') $step = 'profile';
        if($step == 'userPersonal') $step = 'profile';
        if(!$step) $step = 'summary';

        return new RedirectResponse("/dashboard/project/$pid/$step");
    },
        'step' => null
    )

));

// Project delete (old route compatibility)
$prjs->add('project-delete', new Route(
    '/delete/{pid}',
    // array('_controller' => 'Goteo\Controller\ProjectController::deleteAction')
    array('_controller' => function ($pid) {
        return new RedirectResponse("/dashboard/project/$pid/delete");
    })
));


$prjs->add('project-create', new Route(
    '/create',
    array('_controller' => 'Goteo\Controller\ProjectController::createAction')
));

// Favourite project

$prjs->add('project-favourite', new Route(
    '/favourite/{pid}',
    array('_controller' => 'Goteo\Controller\ProjectController::favouriteAction')
));

// Delete Favourite project

$prjs->add('project-delete-favourite', new Route(
    '/delete-favourite',
    array('_controller' => 'Goteo\Controller\ProjectController::DeletefavouriteAction')
));


$prjs->add('project-sections', new Route(
    '/{pid}/{show}/{post}',
    array('_controller' => 'Goteo\Controller\ProjectController::indexAction',
        'id' => null, //optional
        'show' => 'home', //default
        'post' => null //optional
        )
));

return $prjs;
