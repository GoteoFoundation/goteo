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

$misc = new RouteCollection();

///// GLOSSARY /////

$misc->add('glossary', new Route(
    '/glossary',
    array('_controller' => 'Goteo\Controller\GlossaryController::indexAction')
));

///// ABOUT /////

$misc->add('about-librejs', new Route(
    '/about/librejs',
    array(
        '_controller' => 'Goteo\Controller\AboutController::librejsAction',
        )
));

$misc->add('about-sections', new Route(
    '/about/{id}',
    array(
        '_controller' => 'Goteo\Controller\AboutController::indexAction',
        'id' => '' //optional
        )
));

$misc->add('legal-sections', new Route(
    '/legal/{id}',
    array(
        '_controller' => 'Goteo\Controller\AboutController::legalAction',
        'id' => '' //optional
        )
));

// service
$misc->add('service', new Route(
    '/service/{id}',
    array('_controller' => 'Goteo\Controller\AboutController::indexAction')
));

return $misc;
