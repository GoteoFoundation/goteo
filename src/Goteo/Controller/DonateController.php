<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Payment\PaymentException;
use Symfony\Component\HttpFoundation\Request;


class DonateController extends PoolController {

    private string $type = 'donate';

    public function __construct() {
        View::setTheme('responsive');
        if(!Config::get('payments.pool.active')) {
            throw new PaymentException("Pool payment is not active!");
        }
    }

    public function selectAmountDonateAction(Request $request, $landing='yes')
    {
        DashboardController::createSidebar('wallet', 'donate');

        return $this->selectAmountAction($request, $this->type);
    }

    public function selectPaymentMethodDonateAction(Request $request){
        DashboardController::createSidebar('wallet', 'donate');

        return $this->selectPaymentMethodAction($request, $this->type);
    }

    public function paymentFormDonateAction(Request $request){
        DashboardController::createSidebar('wallet', 'donate');

        return $this->paymentFormAction($request, $this->type);
    }

    public function completePaymentDonateAction($invest_id, Request $request){
        DashboardController::createSidebar('wallet', 'donate');

        return $this->completePaymentAction($request, $invest_id, $this->type);
    }

    public function userDataDonateAction($invest_id, Request $request){
        DashboardController::createSidebar('wallet', 'donate');

        return $this->userDataAction($request, $invest_id, $this->type);
    }

    public function shareDonateAction($invest_id, Request $request){
        DashboardController::createSidebar('wallet', 'donate');

        return $this->shareAction($request, $invest_id, $this->type);
    }
}
