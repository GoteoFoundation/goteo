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
use Goteo\Library\Currency;
use Goteo\Library\Listing;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Model\Relief;
use Goteo\Payment\Payment;
use Goteo\Payment\PaymentException;


class PoolController extends \Goteo\Core\Controller {

    private $page = '/pool';
    private $query = '';

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
        if(!Config::get('payments.pool.active')) {
            throw new PaymentException("Pool payment is not active!");
        }
        DashboardController::createSidebar('wallet', 'recharge');
    }

     /**
     * Validates the project availability for investing (redirects or exceptions on failures)
     * Also sets common vars to be used in the views
     * @param  [type] $project_id [description]
     * @param  [type] $reward_id  [description]
     * @param  [type] $amount_id  [description]
     * @return [type]             [description]
     */
    private function validate($amount = null, $login_required = true) {

        $amount_original = (int)$amount;
        $currency = (string)substr($amount, strlen($amount_original));
        if(empty($currency)) $currency = Currency::current('id');
        $currency = Currency::get($currency, 'id');

        $custom_amount = Currency::amountInverse($amount_original, $currency);

        $this->page = '/pool';
        $this->query = http_build_query(['amount' => "$amount_original$currency"]);

        // Available pay methods

        $pay_methods = Payment::getMethods(Session::isLogged() ? Session::getUser() : new User());

        foreach($pay_methods as $i => $method) {
            if(!$method->isPublic()) {
                unset($pay_methods[$i]);
            }
        }

        // Pool disabled

        unset($pay_methods['pool']);

        if($login_required) {

            // A reward is required here
            if($amount_original == 0 && !is_null($amount)) {
                Message::error(Text::get('pool-amount-first'));
                return $this->redirect('/pool');
            }
            // A login is required here
            if(!Session::isLogged()) {

                $login_return='/login?return='.urlencode($this->page . '/payment?' . $this->query);
                Message::info(Text::get('invest-login-alert', $login_return));

                // Message for login page
                Session::store('sub-header', $this->getViewEngine()->render('invest/partials/login_sub_header', ['amount' => $amount_original, 'url_return' => $login_return ]));

                // or login page?
                return $this->redirect('/signup?return=' . urlencode($this->page . '/payment?' . $this->query));
            }
            Session::del('sub-header');
        }

        // Set vars for all views
        $this->contextVars([
            'pay_methods' => $pay_methods,
            'default_method' => Payment::defaultMethod(),
            'amount' => $custom_amount,
            'amount_original' => $amount_original,
            'amount_formated' => Currency::format($amount_original, $currency),
            'currency' => $currency,
            'transaction' => $transaction
        ]);

        return $custom_amount;

    }



    /**
     * step1: Choose rewards
     */
    public function selectAmountAction($landing='null', Request $request)
    {
        // TODO: add events
        $amount = $request->query->get('amount');

        return $this->viewResponse('pool/select_amount', ['step' => 1]);

    }

    /**
     * step2: Choose payment method
     * This method will show a Form on the view that redirects to the payment gateway
     */
    public function selectPaymentMethodAction(Request $request)
    {
        $amount = $request->query->get('amount');

        $amount = $this->validate($amount);
        if($amount instanceOf Response) return $amount;

        //if($reward instanceOf Response) return $reward;

        return $this->viewResponse('pool/payment_method', ['step' => 2]);

    }

    /**
     * step3: send the form to the payment gateway
     * For users without javascript, used shows a form to the payment gateway
     * If called via AJAX, returns a JSON response with the payment gateway form vars
     */
    public function paymentFormAction(Request $request) {
        $amount = $amount_original = $request->query->get('amount');

        $amount_validated = $this->validate($amount);
        if($amount_return instanceOf Response) return $amount_validated;
        $amount = $amount_validated;

        // pay method required
        try {
            $method = Payment::getMethod($request->query->get('method'));

            // Creating the invest entry
            $invest = new Invest(
                array(
                    'amount' => $amount,
                    'amount_original' => $amount_original,
                    'currency' => Currency::current(),
                    'currency_rate' => Currency::rate(),
                    'user' => Session::getUserId(),
                    'project' => NULL,
                    'method' => $method::getId(),
                    'status' => Invest::STATUS_PROCESSING,  // aporte en proceso
                    'invested' => date('Y-m-d')
                )
            );

            $errors = array();
            if (!$invest->save($errors)) {
                throw new \RuntimeException(Text::get('invest-create-error') . '<br />' . implode('<br />', $errors));
            }

            // New Invest Init Event
            $method = $this->dispatch(AppEvents::INVEST_INIT, new FilterInvestInitEvent($invest, $method, $request))->getMethod();

            // go to the gateway, gets the response
            $response = $method->purchase();
            // New Invest Request Event
            $response = $this->dispatch(AppEvents::INVEST_INIT_REQUEST, new FilterInvestRequestEvent($method, $response))->getResponse();

            // Checks and redirects
            if (!$response instanceof ResponseInterface) {
                throw new \RuntimeException('This response does not implements ResponseInterface.');
            }

            // On-sites can return a succesful response here
            if ($response->isSuccessful()) {
                // Event invest success
                return $this->dispatch(AppEvents::INVEST_SUCCEEDED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
            }

            // Redirect to payment platform
            // Event invest redirect
            return $this->dispatch(AppEvents::INVEST_INIT_REDIRECT, new FilterInvestRequestEvent($method, $response))->getHttpResponse();

        } catch(\Exception $e) {
            Message::error($e->getMessage());
            $this->error('Init Payment Exception', ['class' => get_class($e), $invest, 'code' => $e->getCode(), 'message' => $e->getMessage()]);
            return $this->redirect('/pool/payment?' . $this->query);
        }


        //NEVER REACHED...
        $vars = ['pay_method' => $method, 'response' => $response, 'step' => 2];

        if($request->isXmlHttpRequest()) {
            // return JSON
            return $this->jsonResponse([/*TODO*/]);
        }
        return $this->viewResponse('invest/payment_form', $vars);

    }

    /**
     * Returning point
     * When payment is successful, then redirects to step4
     */
    public function completePaymentAction($invest_id, Request $request) {

        $invest = Invest::get($invest_id);

        $amount = $this->validate();
        if($amount instanceOf Response) return $amount;


        if($invest->status != Invest::STATUS_PROCESSING) {
            Message::info(Text::get('invest-process-completed'));
            return $this->redirect('/pool/' . $invest->id);
        }
        try {
            $method = $invest->getMethod();
            $method = $this->dispatch(AppEvents::INVEST_COMPLETE, new FilterInvestInitEvent($invest, $method, $request))->getMethod();
            // Ending transaction
            $response = $method->completePurchase();
            // New Invest Request Event
            $response = $this->dispatch(AppEvents::INVEST_COMPLETE_REQUEST, new FilterInvestRequestEvent($method, $response))->getResponse();

            // Checks and redirects
            if (!$response instanceof ResponseInterface) {
                throw new \RuntimeException('This response does not implements ResponseInterface.');
            }
            if ($response->isSuccessful()) {

                // Goto User data fill
                Message::info(Text::get('invest-payment-success'));

                // Event invest success
                return $this->dispatch(AppEvents::INVEST_SUCCEEDED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
            }

            $msg_fail=Text::get('invest-payment-fail');
            $msg_fail.= $this->debug() ?  ' [ '.$response->getMessage().' ]' : '' ;

            Message::error($msg_fail);

            // Event invest failed
            return $this->dispatch(AppEvents::INVEST_FAILED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();

        } catch(\Exception $e) {
            Message::error($e->getMessage());
            $this->error('Ending Payment Exception', ['class' => get_class($e),  $invest, 'code' => $e->getCode(), 'message' => $e->getMessage()]);
        }
        return $this->redirect('/pool/payment?' . $this->query);
    }

    /**
     * step4: reward/user data
     * Shown when comming back from the payment gateway
     */
    public function userDataAction($invest_id, Request $request)
    {
        $invest = Invest::get($invest_id);
        $amount = $this->validate();
        if($amount instanceOf Response) return $amount;

        // print_r($invest);
        if(!in_array($invest->status, [Invest::STATUS_TO_POOL])) {
            Message::error(Text::get('project-invest-fail'));
            return $this->redirect('/pool/payment?' . $this->query);
        }

        // if resign to reward, redirect to shareAction
        if($invest->resign) {
            // Event invest failed
            return $this->dispatch(AppEvents::INVEST_FINISHED, new FilterInvestFinishEvent($invest, $request))->getHttpResponse();
        }

        // check post data
        $invest_address = (array)$invest->getAddress();
        $errors = [];
        if($request->isMethod('post')) {
            // Event invest failed
            return $this->dispatch(AppEvents::INVEST_FINISHED, new FilterInvestFinishEvent($invest, $request))->getHttpResponse();
        }
        // show form
        return $this->viewResponse('pool/user_data', ['invest' => $invest, 'invest_address' => $invest_address, 'invest_errors' => $errors, 'step' => 3]);

    }

    /**
     * step5: Thanks! and social share
     */
    public function shareAction($invest_id, Request $request) {

        $invest = Invest::get($invest_id);
        $amount = $this->validate();
        if($amount instanceOf Response) return $amount;

        if(!in_array($invest->status, [Invest::STATUS_TO_POOL, Invest::STATUS_PAID])) {
            Message::error(Text::get('project-invest-fail'));
            return $this->redirect('/pool/payment?' . $this->query);
        }

        $lsuf = (LANG != 'es') ? '?lang='.LANG : '';

        $URL = $request->getSchemeAndHttpHost();


        //Get share Twitter and Facebook urls

        $author_twitter = str_replace(
                                array(
                                    'https://',
                                    'http://',
                                    'www.',
                                    'twitter.com/',
                                    '#!/',
                                    '@'
                                ), '', $project->user->twitter);
        $author = !empty($author_twitter) ? ' '.Text::get('regular-by').' @'.$author_twitter : '';

        $share_title = Text::get('pool-invest-spread-social');

        $share_url = $URL . '/pool';
        $facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
        $twitter_url = 'http://twitter.com/home?status=' . urlencode($share_title . ': ' . $share_url);

        $user=Session::getUserId();

        //proyectos que coinciden con mis intereses
        $projects_suggested= Project::favouriteCategories($user);

        if($reward instanceOf Response) return $reward;

        $vars=[ 'facebook_url' => $facebook_url,
                'twitter_url' => $twitter_url,
                'projects_suggested' => $projects_suggested,
                'step' => 4
                ];
        return $this->viewResponse('pool/share',$vars);

    }

}
