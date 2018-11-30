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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Omnipay\Common\Message\ResponseInterface;

use Goteo\Application\Exception\ControllerAccessDeniedException;

use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Application\View;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Application\Event\FilterInvestFinishEvent;
use Goteo\Library\Text;
use Goteo\Application\Currency;
use Goteo\Library\Listing;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Model\Relief;
use Goteo\Payment\Payment;
use Goteo\Payment\PaymentException;


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
