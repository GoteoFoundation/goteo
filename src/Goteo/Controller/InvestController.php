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
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Library\Text;
use Goteo\Library\Currency;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Payment\Payment;
use Goteo\Payment\PaymentException;

class InvestController extends \Goteo\Core\Controller {

    private $page = '/invest';
    private $query = '';

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');

    }

    /**
     * Validates the project availability for investing (redirects or exceptions on failures)
     * Also sets common vars to be used in the views
     * @param  [type] $project_id [description]
     * @param  [type] $reward_id  [description]
     * @param  [type] $amount_id  [description]
     * @return [type]             [description]
     */
    private function validate($project_id, $reward_id = null, &$custom_amount = null, $invest = null, $login_required = true) {

        $project = Project::get($project_id);

        $this->page = '/invest/' . $project_id;
        $this->query = http_build_query(['amount' => $custom_amount, 'reward' => $reward_id]);

        // Security check
        if ($project->status != Project::STATUS_IN_CAMPAIGN) {
            Message::error(Text::get('project-invest-closed'));
            return $this->redirect('/project/' . $project_id, Response::HTTP_TEMPORARY_REDIRECT);
        }

        if ($project->noinvest) {
            Message::error(Text::get('investing_closed'));
            return $this->redirect('/project/' . $project_id);
        }

        // Available pay methods

        $pay_methods = Payment::getMethods(Session::getUser());
        // Is paypal active for the project?
        // This should be more generic...
        if(!Project\Account::getAllowpp($project_id)) {
            unset($pay_methods['paypal']);
        }


        // Find the correct reward/amount
        $reward = null;
        $amount = 0;

        // reward id defined, get the reward and the amount
        if($reward_id && $project->individual_rewards[$reward_id]) {
            $reward = $project->individual_rewards[$reward_id];
            $amount = $reward->amount;
        }
        // Custom amount allowed if are higher
        if($custom_amount > $amount) {
            $amount = $custom_amount;
        }
        // only amount, choose appropiate reward
        if(empty($reward)&&($reward_id!=0)) {
            // Ordered by amount
            foreach($project->individual_rewards as $r) {
                if($custom_amount >= $r->amount && $r->available()) {
                    $reward = $r;
                }
                else {
                    break;
                }
            }
        }

        if($login_required) {
            // A reward is required here
            if(!$invest && empty($reward) && $custom_amount == 0) {
                Message::error('You must choose a reward first!');
                return $this->redirect('/invest/' . $project_id);
            }
            // A login is required here
            if(!Session::isLogged()) {
                Message::error('Please login!');

                // Message for login page
                Session::store('sub-header', $this->getViewEngine()->render('invest/partials/login_sub_header', ['amount' => $amount]));

                // or login page?
                return $this->redirect('/signup?return=' . urlencode($this->page . '/payment?' . $this->query));
            }
            Session::del('sub-header');
        }

        // If invest defined, check user session
        if($invest instanceOf Invest) {
            // user session must be the same as the invest
            if(Session::getUser()->id !== $invest->user) {
                Message::error("You're not allowed to access here!");
                return $this->redirect('/invest/' . $project_id);
            }
            // Get the reward data from the invest
            if($reward = $invest->getReward()) {
                $amount = $reward->amount;
            }
            // if($invest->rewards && is_array($invest->rewards)) {
            //     foreach($invest->rewards as $r) {
            //         if($invest->amount >= $r->amount) {
            //             $reward = $r;
            //             $amount = $r->amount;
            //         }
            //         else {
            //             break;
            //         }
            //     }
            // }
        }

        // print_r($reward);
        // Set vars for all views
        $this->contextVars([
            'project' => $project,
            'pay_methods' => $pay_methods,
            'default_method' => Payment::defaultMethod(),
            'rewards' => $project->individual_rewards,
            'amount' => $amount,
            'reward' => $reward,
            'invest' => $invest
        ]);

        if($reward) {
            $this->query = http_build_query(['amount' => $amount, 'reward' => $reward->id]);
            return $reward;
        }
        return false;
    }

    /**
     * step1: Choose rewards
     */
    public function selectRewardAction($project_id, Request $request)
    {
        // TODO: add events
        $amount = $request->query->get('amount');
        $reward = $this->validate($project_id, $request->query->get('reward'), $amount, null, false);
        if($reward instanceOf Response) return $reward;

        // Aqui cambiar por escoger recompensa
        return $this->viewResponse('invest/select_reward');

    }

    /**
     * step2: Choose payment method
     * This method will show a Form on the view that redirects to the payment gateway
     */
    public function selectPaymentMethodAction($project_id, Request $request)
    {
        $amount = $request->query->get('amount');
        $reward = $this->validate($project_id, $request->query->get('reward'), $amount);

        if($reward instanceOf Response) return $reward;

        return $this->viewResponse('invest/payment_method');

    }

    /**
     * step3: send the form to the payment gateway
     * For users without javascript, used shows a form to the payment gateway
     * If called via AJAX, returns a JSON response with the payment gateway form vars
     */
    public function paymentFormAction($project_id, Request $request) {
        $amount_original = $request->query->get('amount');
        $reward = $this->validate($project_id, $request->query->get('reward'), $amount_original);

        if($reward instanceOf Response) return $reward;

        // pay method required
        try {
            $method = Payment::getMethod($request->query->get('method'));
            // convert from currencies
            $amount = Currency::amount($amount_original);


            // Creating the invest entry
            $invest = new Invest(
                array(
                    'amount' => $amount,
                    'amount_original' => $amount_original,
                    'currency' => Currency::get(),
                    'currency_rate' => Currency::rate(),
                    'user' => Session::getUserId(),
                    'project' => $project_id,
                    'method' => $method::getId(),
                    'status' => Invest::STATUS_PROCESSING,  // aporte en proceso
                    'invested' => date('Y-m-d'),
                    'anonymous' => $request->query->has('anonymous') ? true : false,
                    'resign' => $reward ? false : true,
                    'pool' => $request->query->has('pool_on_fail') ? true : false
                )
            );

            // Rewards
            $invest->rewards = $reward ? [$reward->id] : null;

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
            $this->getService('paylogger')->error('Init Payment Exception: ' . get_class($e) . ' CODE: ' . $e->getCode() . ' MESSAGE: ' . $e->getMessage());
            return $this->redirect('/invest/' . $project_id . '/payment?' . $this->query);
        }


        $vars = ['pay_method' => $method, 'response' => $response];

        if($request->isXmlHttpRequest()) {
            // return JSON
            return $this->jsonResponse([/*TODO*/]);
        }
        return $this->viewResponse('invest/payment_form', $vars);

    }

    /**
     * For off-site payments with async communication
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function notifyPaymentAction($method, Request $request) {
        $this->getService('paylogger')->debug('Payment Notification Access. USER AGENT: ' . $request->server->get('HTTP_USER_AGENT') . ' GET: ' . print_r($request->query->all(), 1) .' POST: ' . print_r($request->request->all(), 1));
        try {
            $method = Payment::getMethod($method);
            $method->setRequest($request);

            $response = $method->completePurchase();
            $invest = $method->getInvest();

            // Invest valid check
            if (!$invest instanceof Invest) {
                throw new \RuntimeException('The notify completePurchase() must obtain a valid Invest object');
            }

            if($invest->status != Invest::STATUS_PROCESSING) {
                $this->getService('paylogger')->warn('Payment Notification Duplicated INVEST: [' . $invest->id . ']. USER AGENT: ' . $request->server->get('HTTP_USER_AGENT') . ' GET: ' . print_r($request->query->all(), 1) .' POST: ' . print_r($request->request->all(), 1));
                return $this->redirect('/invest/' . $project_id . '/' . $invest->id);
            }

            // New Invest Notify Event (a HttpResponse will be assigned here)
            $response = $this->dispatch(AppEvents::INVEST_NOTIFY, new FilterInvestRequestEvent($method, $response))->getResponse();

            // Checks
            if (!$response instanceof ResponseInterface) {
                throw new \RuntimeException('This response does not implements ResponseInterface.');
            }
            if ($response->isSuccessful()) {
                // Event invest success
                return $this->dispatch(AppEvents::INVEST_SUCCEEDED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
            }

            // Event invest failed
            return $this->dispatch(AppEvents::INVEST_FAILED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();


        } catch(\Exception $e) {
            $this->getService('paylogger')->error('Payment Notification Exception: ' . get_class($e) . ' CODE: ' . $e->getCode() . ' MESSAGE: ' . $e->getMessage());
        }
        return $this->redirect('/');
    }

    /**
     * Returning point
     * When payment is successful, then redirects to step4
     */
    public function completePaymentAction($project_id, $invest_id, Request $request) {
        $invest = Invest::get($invest_id);

        $reward = $this->validate($project_id, null, $_dummy, $invest);
        if($reward instanceOf Response) return $reward;

        if($invest->status != Invest::STATUS_PROCESSING) {
            Message::info(Text::get('invest-process-completed'));
            return $this->redirect('/invest/' . $project_id . '/' . $invest->id);
        }

        try {
            $method = Payment::getMethod($invest->method);
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
                // Event invest success
                return $this->dispatch(AppEvents::INVEST_SUCCEEDED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
            }

            // Event invest failed
            return $this->dispatch(AppEvents::INVEST_FAILED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();

        } catch(\Exception $e) {
            Message::error($e->getMessage());
            $this->getService('paylogger')->error('Ending Payment Exception: ' . get_class($e) . ' CODE: ' . $e->getCode() . ' MESSAGE: ' . $e->getMessage());
        }
        return $this->redirect('/invest/' . $project_id . '/payment?' . $this->query);
    }

    /**
     * step4: reward/user data
     * Shown when comming back from the payment gateway
     */
    public function userDataAction($project_id, $invest_id, Request $request)
    {
        $invest = Invest::get($invest_id);
        $reward = $this->validate($project_id, null, $_dummy, $invest);

        if($reward instanceOf Response) return $reward;

        if(!in_array($invest->status, [Invest::STATUS_CHARGED, Invest::STATUS_PAID])) {
            Message::error(Text::get('project-invest-fail'));
            return $this->redirect('/invest/' . $project_id . '/payment?' . $this->query);
        }

        // Aqui cambiar por escoger recompensa
        return $this->viewResponse('invest/user_data');

    }

    /**
     * step5: Thanks! and social share
     */
    public function shareAction($project_id, $invest_id, Request $request) {

        $invest = Invest::get($invest_id);
        $reward = $this->validate($project_id, null, $_dummy, $invest);

        if($reward instanceOf Response) return $reward;

        if(!in_array($invest->status, [Invest::STATUS_CHARGED, Invest::STATUS_PAID])) {
            Message::error(Text::get('project-invest-fail'));
            return $this->redirect('/invest/' . $project_id . '/payment?' . $this->query);
        }

        $lsuf = (LANG != 'es') ? '?lang='.LANG : '';

        $URL = $request->getSchemeAndHttpHost();

        //Get widgets code

        $url = $URL . '/widget/project/' . $project_id;

        $widget_code = Text::widget($url . $lsuf);

        $widget_code_investor = Text::widget($url.'/invested/'.$user->id.'/'.$lsuf);

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
        $share_title = Text::get('project-spread-social', $project->name . $author);

        if (!\Goteo\Application\Config::isMasterNode())
            $share_title = str_replace ('#goteo', '#'.strtolower (NODE_NAME), $share_title);

        $share_url = $URL . '/project/'.$project->id;
        $facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
        $twitter_url = 'http://twitter.com/home?status=' . urlencode($share_title . ': ' . $share_url);

        if($reward instanceOf Response) return $reward;
        return $this->viewResponse(
                'invest/share',
                array(
                    'facebook_url' => $facebook_url,
                    'twitter_url' => $twitter_url,
                    'widget_code' => $widget_code,
                    'widget_code_investor' => $widget_code
                )
        );
    }

}
