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

use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Application\View;
use Goteo\Application\Lang;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Application\Event\FilterInvestFinishEvent;
use Goteo\Application\Event\FilterAuthEvent;
use Goteo\Library\Text;
use Goteo\Application\Currency;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Payment\Payment;
use Goteo\Payment\PaymentException;
use Goteo\Util\Monolog\Processor\WebProcessor;

class InvestRecoverController extends \Goteo\Core\Controller {
    private $invests = [];
    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
        $this->invests = Config::get('plugins.invest-return.invests');
    }

    public function recoverAction($project_id, $id, Request $request) {
        $invest = Invest::get($id);
        $pid = $invest->project ?: $project_id;
        Session::del('recover-invest');
        if(!$invest) {
            Message::error(Text::get('invest-not-found'));
            return $this->redirect("/invest/$pid");
        }

        if(!in_array($id, $this->invests)) {
            Message::error(Text::get('invest-return-already-processed'));
        // die("$pid");
            return $this->redirect("/invest/$pid");
        }
        if(!$invest->isCharged()) {
            Message::error(Text::get('invest-return-already-processed'));
            return $this->redirect("/invest/$pid");
        }
        $user = $invest->getUser();

        Session::store('recover-invest', $invest);
        // $reward = $invest->getFirstReward();
        // $url = "/invest/$pid/payment?amount=" . $invest->amount . $invest->currency .  ($reward ? "&reward={$reward->id}" : '');
        $url = "/invest/$pid/payment?amount=" . $invest->amount . $invest->currency;
        // die($url);
        return $this->redirect($url);
    }
}
