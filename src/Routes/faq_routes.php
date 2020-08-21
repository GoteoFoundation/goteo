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
use Symfony\Component\HttpFoundation\Request;


$faq = new RouteCollection();

//// FAQ /////



// New all-in-one controller
$faq->add('faq-section', new Route(
    '/{section}',
    [
        '_controller' => 'Goteo\Controller\FaqController::sectionAction'
    ]
));



return $faq;
