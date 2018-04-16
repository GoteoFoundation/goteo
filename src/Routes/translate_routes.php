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

$trans = new RouteCollection();

//// TRANSLATE ////

$trans->add('translate-list-texts', new Route(
    '/texts',
    array('_controller' => 'Goteo\Controller\TranslateController::listTextAction',
        )
));
$trans->add('translate-list', new Route(
    '/{zone}',
    array('_controller' => 'Goteo\Controller\TranslateController::listAction',
        )
));

$trans->add('translate-edit-texts', new Route(
    '/texts/{id}',
    array('_controller' => 'Goteo\Controller\TranslateController::editTextAction',
        )
));

$trans->add('translate-edit', new Route(
    '/{zone}/{id}',
    array('_controller' => 'Goteo\Controller\TranslateController::editAction',
        )
));

//Compatibility redirect for old links
$trans->add('translate-old-edit', new Route(
    '/{zone}/edit/{id}',
    array('_controller' => function ($zone, $id) {
            return new RedirectResponse("/translate/$zone/$id");
        })
    )
);

return $trans;
