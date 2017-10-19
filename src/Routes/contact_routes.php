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

$contact = new RouteCollection();

//// CONTACT ////

//new captcha
$contact->add('contact-captcha', new Route(
    '/captcha',
    array(
        '_controller' => 'Goteo\Controller\ContactController::captchaAction'
        )
));

return $contact;
