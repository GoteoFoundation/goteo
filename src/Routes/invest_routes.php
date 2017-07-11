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

$invest = new RouteCollection();


////// INVEST //////
///
/// /project/project-name/invest should be the same as /invest/project-name
$invest->add('invest', new Route(
    '/{project_id}',
    array('_controller' => 'Goteo\Controller\InvestController::selectRewardAction',
        )
));


/// This is for compatibility with old routes
$invest->add('invest-old-route', new Route(
    '/project/{project_id}/invest',
    array('_controller' => 'Goteo\Controller\InvestController::selectRewardAction',
        )
));
$invest->add('invest-select-payment', new Route(
    '/{project_id}/payment',
    array('_controller' => 'Goteo\Controller\InvestController::selectPaymentMethodAction',
        )
));

// Custom login for invest process
$routes->add('invest-login', new Route(
    '/invest/{project_id}/login',
    array('_controller' => 'Goteo\Controller\InvestController::loginAction',
        )
));
$routes->add('invest-signup', new Route(
    '/invest/{project_id}/signup',
    array('_controller' => 'Goteo\Controller\InvestController::signupAction',
        )
));

// ¿ optional step ? may skipped by javascript ?
$invest->add('invest-show-form', new Route(
    '/{project_id}/form',
    array('_controller' => 'Goteo\Controller\InvestController::paymentFormAction',
        )
));
// Notify URL for gateways that need it
$invest->add('invest-gateway-notify', new Route(
    '/notify/{method}',
    array('_controller' => 'Goteo\Controller\InvestController::notifyPaymentAction',
        )
));
// Payment gateways returning points
$invest->add('invest-gateway-complete', new Route(
    '/{project_id}/{invest_id}/complete',
    array('_controller' => 'Goteo\Controller\InvestController::completePaymentAction',
        )
));
$invest->add('invest-user-data', new Route(
    '/{project_id}/{invest_id}',
    array('_controller' => 'Goteo\Controller\InvestController::userDataAction',
        ),
    array('invest_id' => '[0-9]+')
));
$invest->add('invest-share', new Route(
    '/{project_id}/{invest_id}/share',
    array('_controller' => 'Goteo\Controller\InvestController::shareAction',
        )
));

$invest->add('invest-msg-support', new Route(
    '/{project_id}/{invest_id}/support-msg',
    array('_controller' => 'Goteo\Controller\InvestController::supportMsgAction',
        )
));


return $invest;
