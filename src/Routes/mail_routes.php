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

$mail = new RouteCollection();

////// MAILING /////
$mail->add('mail-track', new Route(
    '/track/{token}.gif',
    array('_controller' => 'Goteo\Controller\MailController::trackAction')
));
$mail->add('mail-link', new Route(
    '/link/{id}',
    array('_controller' => 'Goteo\Controller\MailController::linkAction')
));
$mail->add('mail-url', new Route(
    '/url/{token}',
    array('_controller' => 'Goteo\Controller\MailController::urlAction')
));
$mail->add('mail-token', new Route(
    '/{token}',
    array('_controller' => 'Goteo\Controller\MailController::indexAction')
));


return $mail;
