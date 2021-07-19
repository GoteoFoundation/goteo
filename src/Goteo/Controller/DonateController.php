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
use Goteo\Model\Relief;
use Goteo\Payment\PaymentException;
use Symfony\Component\HttpFoundation\Request;


class DonateController extends PoolController {

    private $page = '/donate';
    private $query = '';
    private $type='donate';

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
        if(!Config::get('payments.pool.active')) {
            throw new PaymentException("Pool payment is not active!");
        }
    }

    public function donateLandingAction(Request $request)
    {
        return $this->viewResponse('donate/donate',
                [
                    'no_donor_button' => 1
                ]
        );
    }

    public function selectAmountDonateAction($landing='yes', Request $request)
    {
        DashboardController::createSidebar('wallet', 'donate');

        return $this->selectAmountAction($landing, $this->type, $request);
    }

    public function selectPaymentMethodDonateAction(Request $request){
        DashboardController::createSidebar('wallet', 'donate');

        return $this->selectPaymentMethodAction($this->type, $request);
    }

    public function paymentFormDonateAction(Request $request){
        DashboardController::createSidebar('wallet', 'donate');
        return $this->paymentFormAction($this->type, $request);
    }

    public function completePaymentDonateAction($invest_id, Request $request){
        DashboardController::createSidebar('wallet', 'donate');
        return $this->completePaymentAction($invest_id, $this->type, $request);
    }

    public function userDataDonateAction($invest_id, Request $request){
        DashboardController::createSidebar('wallet', 'donate');
        return $this->userDataAction($invest_id, $this->type, $request);
    }

    public function shareDonateAction($invest_id, Request $request){
        DashboardController::createSidebar('wallet', 'donate');
        return $this->shareAction($invest_id, $this->type, $request);
    }
}
