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

$pool = new RouteCollection();

////// Pool rechargue //////
///


$pool->add('pool-select-payment', new Route(
    '/payment',
    array('_controller' => 'Goteo\Controller\PoolController::selectPaymentMethodAction',
        )
));

$pool->add('pool-show-form', new Route(
    '/form',
    array('_controller' => 'Goteo\Controller\PoolController::paymentFormAction',
        )
));

// Payment gateways returning points
$pool->add('pool-invest-gateway-complete', new Route(
    '/{invest_id}/complete',
    array('_controller' => 'Goteo\Controller\PoolController::completePaymentAction',
        )
));


$pool->add('pool-invest-user-data', new Route(
    '/{invest_id}',
    array('_controller' => 'Goteo\Controller\PoolController::userDataAction',
        ),
    array('invest_id' => '[0-9]+')
));

$pool->add('pool-invest-share', new Route(
    '/{invest_id}/share',
    array('_controller' => 'Goteo\Controller\PoolController::shareAction',
        )
));


return $pool;
