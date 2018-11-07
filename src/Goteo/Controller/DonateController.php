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

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
        if(!Config::get('payments.pool.active')) {
            throw new PaymentException("Pool payment is not active!");
        }
        DashboardController::createSidebar('donate', 'recharge');
    }

    public function selectAmountDonateAction($landing='yes', Request $request)
    {
        $this->selectAmountAction($landing, $request);
    }

   
}
