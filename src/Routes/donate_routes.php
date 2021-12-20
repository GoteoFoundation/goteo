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

$donate = new RouteCollection();

////// donate routes //////
///


// Select donation
$donate->add('donate-select-amount', new Route(
    '/select',
    array('_controller' => 'Goteo\Controller\DonateController::selectAmountDonateAction')
));

$donate->add('donate-select-payment', new Route(
    '/payment',
    array('_controller' => 'Goteo\Controller\DonateController::selectPaymentMethodDonateAction',)
));

$donate->add('donate-show-form', new Route(
    '/form',
    array('_controller' => 'Goteo\Controller\DonateController::paymentFormDonateAction',)
));

// Payment gateways returning points
$donate->add('donate-invest-gateway-complete', new Route(
    '/{invest_id}/complete',
    array('_controller' => 'Goteo\Controller\DonateController::completePaymentDonateAction',)
));

$donate->add('donate-invest-user-data', new Route(
    '/{invest_id}',
    array('_controller' => 'Goteo\Controller\DonateController::userDataDonateAction',
        ),
    array('invest_id' => '[0-9]+')
));

$donate->add('donate-invest-share', new Route(
    '/{invest_id}/share',
    array('_controller' => 'Goteo\Controller\DonateController::shareDonateAction',)
));

return $donate;
