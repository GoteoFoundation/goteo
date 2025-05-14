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

use Exception;
use Goteo\Application\App;
use Goteo\Application\AppEvents;
use Goteo\Application\Config;
use Goteo\Application\Currency;
use Goteo\Application\Event\FilterInvestFinishEvent;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Library\Domain;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\Project;
use Goteo\Model\Project\Reward;
use Goteo\Model\User;
use Goteo\Payment\Payment;
use Goteo\Util\Monolog\Processor\WebProcessor;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Stripe\Subscription\Message\DonationResponse;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InvestController extends Controller {

    private string $page = '/invest';
    private string $query = '';

    public function __construct() {
        View::setTheme('responsive');
    }

    /**
     * Log here to channel payment
     * @param  string $func    [description]
     */
    public function log($message, array $context = [], $func = 'info') {
        $logger = App::getService('paylogger');
        if (null !== $logger && method_exists($logger, $func)) {
            return $logger->$func($message, WebProcessor::processObject($context));
        }
    }

    protected function getUser(): ?User
    {
        $user = Session::getUser();
        if(!$user) {
            $user = Session::get('fake-user');
        }
        return $user;
    }

    /**
     * Validates the project availability for investing (redirects or exceptions on failures)
     * Also sets common vars to be used in the views
     *
     * if $login_required is 'auto', login will be required depending on
     * the skip_login variable from project configuration
     */
    private function validate(
        $project_id, $reward_id = null, &$custom_amount = null, $invest = null, $login_required = true, ?Request $request = null
    ) {
        $project = Project::get($project_id, Lang::current());
        // Add analytics to config
        // TODO: do the same with facebook pixel (not done yet because f.pixel is only used in the project page)
        if($project->analytics_id) {
            Config::set('analytics.google', array_merge(Config::get('analytics.google'), [$project->analytics_id]));
        }

        $this->project = $project;

        $amount_original = (int)$custom_amount;
        $currency = (string)substr($custom_amount, strlen($amount_original));
        if(empty($currency)) $currency = Currency::current('id');
        $currency = Currency::get($currency, 'id');

        $custom_amount = Currency::amountInverse($amount_original, $currency);
        $project_categories = Project\Category::getNames($project_id);
        $donate_amount = Currency::amount(
            Config::get('donate.tip_amount'),
            Config::get('currency')
        );

        if ($request) {
            $return_to = '';

            if ($request->query->has('return_to')) {
                $return_to = $request->query->get('return_to');

                if (Domain::isAllowedDomain($return_to))
                    Session::store('return_to', $return_to);
            }
        }

        $this->page = '/invest/' . $project_id;
        $this->query = http_build_query([
            'amount' => "$amount_original$currency",
            'reward' => $reward_id,
            'donate_amount' => "$donate_amount$currency",
            'return_to' => $return_to
        ]);

        // Some projects may have activated a non-registering investion
        $this->skip_login = Session::isLogged() ? false : $project->getAccount()->skip_login;

        // Security check
        if ($project->status != Project::STATUS_IN_CAMPAIGN) {
            Message::error(Text::get('project-invest-closed'));
            return $this->redirect('/project/' . $project_id, Response::HTTP_TEMPORARY_REDIRECT);
        }

        if ($project->noinvest) {
            Message::error(Text::get('investing_closed'));
            return $this->redirect('/project/' . $project_id);
        }

        $pay_methods = Payment::getMethods(Session::isLogged() ? Session::getUser() : new User());

        foreach($pay_methods as $i => $method) {
            if(!$method->isPublic()) {
                unset($pay_methods[$i]);
            }
        }
        // Is paypal active for the project?
        // This should be more generic...
        if(!Project\Account::getAllowpp($project_id)) {
            unset($pay_methods['paypal']);
        }

        if(!Project\Account::getAllowStripe($project_id)) {
            unset($pay_methods['stripe']);
            unset($pay_methods['stripe_subscription']);
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
        // TODO: check this
        if($custom_amount < $amount) {
            $custom_amount = $amount;
            $amount_original = Currency::amount($amount, $currency);
        }
        if(!$reward && $reward_id) {
            Message::error(Text::get('invest-reward-not-found'));
            return $this->redirect('/invest/' . $project_id);
        }

        // only amount, choose appropriate reward
        if(empty($reward) && $reward_id != 0) {
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

        if ($reward->subscribable) {
            $pay_methods = array_intersect_key($pay_methods, array_flip(['stripe_subscription']));
        }

        if($login_required) {
            // A reward is required here
            if(!$invest && empty($reward) && $custom_amount == 0) {
                Message::error(Text::get('invest-reward-first'));
                return $this->redirect('/invest/' . $project_id);
            }
        }

        // A login or a fake login is required here
        if($login_required === 'auto') $login_required = !$this->skip_login;
        if($login_required && !$this->getUser()) {
            return $this->redirect('/invest/' . $project->id . '/signup?' . $this->query);
        }

        // If invest defined, check user session
        if($invest instanceOf Invest) {
            // user session must be the same as the invest
            if($this->getUser()->id !== $invest->user) {
                Message::error(Text::get('auth-access-forbbiden'));
                return $this->redirect('/invest/' . $project_id);
            }
            // Get the reward data from the invest
            if($reward = $invest->getFirstReward()) {
                $amount = $reward->amount;
            }
        }

        $matchers = $project->getMatchers();

        $this->contextVars([
            'project' => $project,
            'skip_login' => $this->skip_login,
            'project_categories' => $project_categories,
            'pay_methods' => $pay_methods,
            'default_method' => Payment::defaultMethod(),
            'rewards' => $project->individual_rewards,
            'amount' => $custom_amount,
            'amount_original' => $amount_original,
            'amount_formated' => Currency::format($amount_original, $currency),
            'donate_amount' => $donate_amount,
            'currency' => $currency,
            'reward' => $reward,
            'query' => $this->query,
            'invest' => $invest,
            'matchers' => $matchers
        ]);

        if($reward) {
            if(!$reward->available() && !$reward->inInvest($invest)) {
                Message::error(Text::get('invest-reward-used-up'));
                return $this->redirect('/invest/' . $project_id);
            }

            $this->query = http_build_query([
                'amount' => "$custom_amount$currency",
                'reward' => $reward->id,
                'donate_amount' => "$donate_amount$currency"
            ]);
            return $reward;
        }

        return false;
    }

    /**
     * step1: Choose rewards
     */
    public function selectRewardAction($project_id, Request $request): Response
    {
        // TODO: add events
        $amount = $request->query->get('amount');
        $reward = $this->validate($project_id, $request->query->get('reward'), $amount, null, false, $request);
        if($reward instanceOf Response) return $reward;

        // Aqui cambiar por escoger recompensa

        $rewards_mosaic = Project::showRewardsMosaic($project_id);
        $view= $rewards_mosaic ? 'select_reward_mosaic' : 'select_reward';

        return $this->viewResponse('invest/'.$view, ['step' => 1]);
    }

    /**
     * step1.5: login page
     */
    public function loginAction($project_id, Request $request)
    {
        $amount = $request->query->get('amount');
        $reward = $this->validate($project_id, $request->query->get('reward'), $amount, null, false, $request);

        if($reward instanceOf Response) return $reward;
        if(!$request->query->has('return')) {
            $request->query->set('return', $this->page . '/payment?' . $this->query);
        }

        $result = AuthController::checkLogin($request);
        if($result instanceOf Response) return $result;

        return $this->viewResponse(
            'invest/login',
            ['step' => 2, 'return' => $this->page . '/payment?' . $this->query]
        );
    }

    /**
     * step1.5: signup page
     */
    public function signupAction($project_id, Request $request)
    {
        $amount = $request->query->get('amount');
        $reward = $this->validate($project_id, $request->query->get('reward'), $amount, null, false, $request);

        if($reward instanceOf Response) return $reward;
        if(!$request->query->has('return')) {
            $request->query->set('return', $this->page . '/payment?' . $this->query);
        }

        $result = AuthController::checkSignup($request);
        if($result instanceOf Response) return $result;

        return $this->viewResponse(
            'invest/signup',
            $result + ['step' => 2, 'return' => $this->page . '/payment?' . $this->query]
        );
    }

    /**
     * step2: Choose payment method
     * This method will show a Form on the view that redirects to the payment gateway
     */
    public function selectPaymentMethodAction(Request $request, $project_id)
    {
        $amount = $request->query->get('amount');
        $donate_amount = $request->query->getInt('donate_amount', Config::get('donate.tip_amount'));
        $email = $request->query->has('email');
        $reward = $this->validate($project_id, $request->query->get('reward'), $amount, null, 'auto', $request);

        if(!($this->skip_login && $email) && !Session::isLogged()) {
            return $this->redirect('/invest/' . $project_id . '/signup?' . $this->query);
        }

        if($reward instanceOf Response) return $reward;
        $vars = ['step' => 2];

        // Donate amount
        $vars['donate_amount']= Currency::amountInverse($donate_amount, Currency::current());

        // tip to the platform active
        $vars['tip']= Config::get('donate.tip');
        if($this->skip_login) {
            $vars['email'] = $this->getUser() ? $this->getUser()->email : '';
            if($request->query->get('email')) {
                $vars['email'] = $request->query->get('email');
            }
            $vars['name'] = $this->getUser() ? $this->getUser()->name : '';
            if($request->query->get('name')) {
                $vars['name'] = $request->query->get('name');
            }
            $vars['errors'] = Session::getAndDel('user-create-errors');
        }

        return $this->viewResponse('invest/payment_method', $vars);
    }

    /**
     * step3: send the form to the payment gateway
     * For users without javascript, used shows a form to the payment gateway
     * If called via AJAX, returns a JSON response with the payment gateway form vars
     */
    public function paymentFormAction($project_id, Request $request) {
        $currency = Currency::current('id');
        $tip=$request->query->get('tip');
        $donate_amount =  $tip ? $request->query->get('donate_amount') : 0;
        $amount = $amount_original = $request->query->get('amount');
        $reward = $this->validate($project_id, $request->query->get('reward'), $amount, null, 'auto', $request);

        if($reward instanceOf Response) return $reward;

        if($this->skip_login) {
            if($request->query->has('email'))
                $this->query .= '&email=' . urlencode($request->query->get('email'));
            if($request->query->has('name'))
                $this->query .= '&name=' . urlencode($request->query->get('name'));
        }

        // pay method required
        try {
            $user = Session::getUser();
            // No login registering, check if user exists and it's a ghost
            // (no password, no social-login)
            if($this->skip_login && !$user) {
                $errors = [];
                $email = $request->query->get('email');
                $name = trim($request->query->get('name'));
                if(!$request->query->has('anonymous') && !$name) {
                    $errors['name'] = Text::get('invest-user-name-or-anonymous');
                }
                if(!$request->query->has('register_accept') && !$name) {
                    $errors['register_accept'] = Text::get('error-register-accept');
                }
                if($errors) {
                    Session::store('user-create-errors', $errors);
                    throw new RuntimeException(Text::get('invest-create-error') . '<br />' . implode('<br />', $errors));
                }
                if(!$name) $name = ucfirst(strtok($email, '@'));
                $suggest = User::suggestUserId($email);
                if(!$user = User::getByEmail($email)) {
                    $user = new User([
                        'email' => $email,
                        'name' => $name,
                        'userid' => $suggest[0],
                        'active' => true,
                        'node' => Config::get('current_node')
                    ]);
                    if(!$user->save($errors, ['password', 'active'])) {
                        Session::store('user-create-errors', $errors);
                        throw new RuntimeException(Text::get('invest-create-error') . '<br />' . implode('<br />', $errors));
                    }
                    // Remove newsletter options
                    User::setPreferences($user->id, array('mailing' => 1));
                }
                if(!$user->isGhost()) {
                    Session::store('user-create-errors', ['email' => Text::get('invest-user-not-ghost')]);
                    throw new RuntimeException(Text::get('invest-user-not-ghost'));
                }
                Session::store('fake-user', $user);
            }

            $method = Payment::getMethod($request->query->get('method'));

            $invest = new Invest(
                array(
                    'amount' => $amount,
                    'donate_amount' => Currency::amountInverse($donate_amount, $currency),
                    'amount_original' => $amount_original+$donate_amount,
                    'currency' => Currency::current(),
                    'currency_rate' => Currency::rate(),
                    'user' => $user->id,
                    'project' => $project_id,
                    'method' => $method->getIdNonStatic(),
                    'status' => Invest::STATUS_PROCESSING,
                    'invested' => date('Y-m-d'),
                    'anonymous' => $request->query->has('anonymous'),
                    'resign' => $reward ? false : true,
                    'pool' => $request->query->has('pool_on_fail')
                )
            );

            $invest->rewards = $reward ? [$reward->id] : null;
            $errors = [];

            if (!$invest->save($errors)) {
                throw new RuntimeException(Text::get('invest-create-error') . '<br />' . implode('<br />', $errors));
            }

            $method = $this->dispatch(AppEvents::INVEST_INIT, new FilterInvestInitEvent($invest, $method, $request))->getMethod();

            // go to the gateway, gets the response
            $response = $method->purchase();
            $response = $this->dispatch(AppEvents::INVEST_INIT_REQUEST, new FilterInvestRequestEvent($method, $response))->getResponse();

            // Checks and redirects
            if (!$response instanceof ResponseInterface) {
                throw new RuntimeException('This response does not implements ResponseInterface.');
            }

            if ($response->isSuccessful()) {
                return $this->dispatch(AppEvents::INVEST_SUCCEEDED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
            }

            return $this->dispatch(AppEvents::INVEST_INIT_REDIRECT, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
        } catch(Exception $e) {
            Message::error($e->getMessage());
            $this->error('Init Payment Exception', ['class' => get_class($e), $invest, 'code' => $e->getCode(), 'message' => $e->getMessage()]);

            return $this->redirect('/invest/' . $project_id . '/payment?' . $this->query);
        }

        //NEVER REACHED...
        $vars = ['pay_method' => $method, 'response' => $response, 'step' => 2];

        if($request->isXmlHttpRequest()) {
            return $this->jsonResponse([/*TODO*/]);
        }
        return $this->viewResponse('invest/payment_form', $vars);
    }

    /**
     * For off-site payments with async communication
     */
    public function notifyPaymentAction($method, Request $request) {
        $this->debug(
            'Payment Notification Access',
            ['user_agent' => $request->server->get('HTTP_USER_AGENT'), 'get' => $request->query->all(), 'post' =>$request->request->all()]
        );
        try {
            $method = Payment::getMethod($method);
            $method->setRequest($request);

            $invest = $method->getInvest();

            // Invest valid check
            if (!$invest instanceof Invest) {
                throw new RuntimeException('The setRequest() should provide a valid Invest object in notifyPaymentAction');
            }

            // Some weird payment gateways may send you the same notification and then charge again
            // Abort if invest is not pending
            if($invest->isPending()) {
                $response = $method->completePurchase();
            } else {
                $this->critical('Payment Notification Duplicated', [$invest, $invest->getUser(), 'user_agent' => $request->server->get('HTTP_USER_AGENT'), 'get' => $request->query->all(), 'post' => $request->request->all()]);
                if($invest->getProject()) {
                    return $this->redirect('/invest/' . $invest->getProject()->id . '/' . $invest->id);
                }
                // This case belongs to pool controller
                // It's here because some Gateways doesn't allow to change the notify URL
                return $this->redirect('/pool/' . $invest->id);
            }

            $response = $this->dispatch(AppEvents::INVEST_NOTIFY, new FilterInvestRequestEvent($method, $response))->getResponse();

            // Checks
            if (!$response instanceof ResponseInterface) {
                throw new RuntimeException('This response does not implements ResponseInterface.');
            }
            if ($response->isSuccessful()) {
                return $this->dispatch(AppEvents::INVEST_SUCCEEDED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
            }

            return $this->dispatch(AppEvents::INVEST_FAILED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
        } catch(Exception $e) {
            $this->error('Payment Notification Exception', ['class' => get_class($e), $invest, 'code' => $e->getCode(), 'message' => $e->getMessage()]);
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

        // If the payment has online communication the payment process has been processed by
        // the notifyAction therefore redirect the user to the final stage here
        if (!$invest->isPending()) {
            Message::info(Text::get('invest-process-completed'));
            return $this->redirect('/invest/' . $project_id . '/' . $invest->id);
        }

        try {
            $method = $invest->getMethod();
            $method = $this->dispatch(AppEvents::INVEST_COMPLETE, new FilterInvestInitEvent($invest, $method, $request))->getMethod();
            // Ending transaction
            $response = $method->completePurchase();
            $response = $this->dispatch(AppEvents::INVEST_COMPLETE_REQUEST, new FilterInvestRequestEvent($method, $response))->getResponse();

            // Checks and redirects
            if (!$response instanceof ResponseInterface) {
                throw new RuntimeException('This response does not implements ResponseInterface.');
            }

            if ($response instanceof DonationResponse) {
                return $this->dispatch(AppEvents::INVEST_INIT_REDIRECT, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
            }

            if ($response->isSuccessful()) {
                // Goto User data fill
                Message::info(Text::get('invest-payment-success'));

                return $this->dispatch(AppEvents::INVEST_SUCCEEDED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
            }

            $msg_fail = Text::get('invest-payment-fail');
            $msg_fail.= App::debug() ?  ' [ '.$response->getMessage().' ]' : '' ;

            Message::error($msg_fail);

            return $this->dispatch(AppEvents::INVEST_FAILED, new FilterInvestRequestEvent($method, $response))->getHttpResponse();
        } catch(Exception $e) {
            Message::error($e->getMessage());
            $this->error('Ending Payment Exception', ['class' => get_class($e),  $invest, 'code' => $e->getCode(), 'message' => $e->getMessage()]);
        }

        return $this->redirect('/invest/' . $project_id . '/payment?' . $this->query);
    }

    /**
     * step4: reward/user data
     * Shown when coming back from the payment gateway
     */
    public function userDataAction($project_id, $invest_id, Request $request)
    {
        $invest = Invest::get($invest_id);
        $reward = $this->validate($project_id, null, $_dummy, $invest);

        if($reward instanceOf Response) return $reward;
        $reward = Reward::get($reward->id, Lang::current());

        if(!in_array($invest->status, [Invest::STATUS_CHARGED, Invest::STATUS_PAID])) {
            Message::error(Text::get('project-invest-fail'));
            return $this->redirect('/invest/' . $project_id . '/payment?' . $this->query);
        }

        // if resign to reward, redirect to shareAction
        if($request->isMethod(Request::METHOD_POST) && $invest->resign) {
            return $this->dispatch(AppEvents::INVEST_FINISHED, new FilterInvestFinishEvent($invest, $request))->getHttpResponse();
        }

        // check post data
        $invest_address = (array)$invest->getAddress();
        $errors = [];
        if($request->isMethod(Request::METHOD_POST) && $request->request->has('fiscal')) {
            $invest_address = $request->request->get('invest');
            if(is_array($invest_address)) {
                $ok = true;
                foreach(['name', 'address', 'zipcode', 'location', 'country'] as $part) {
                    $invest_address[$part] = trim($invest_address[$part]);
                    if(empty($invest_address[$part])) {
                        $ok = false;
                        $errors[] = $part;
                    }
                }
                $invest->extra_info = $invest_address['extra_info'];
                $invest->save($errors);

                $isAddressValid = $invest->setAddress($invest_address);
                if($ok && $isAddressValid) {
                    return $this->dispatch(AppEvents::INVEST_FINISHED, new FilterInvestFinishEvent($invest, $request))->getHttpResponse();
                }
            }
            Message::error(Text::get('invest-address-fail'));
            Message::error(implode(',', $errors));
        }

        return $this->viewResponse(
            'invest/user_data',
            ['invest_address' => $invest_address, 'invest_errors' => $errors, 'step' => 3, 'reward' => $reward]
        );
    }

    /**
     * step5: Thanks! and social share
     */
    public function shareAction($project_id, $invest_id, Request $request) {

        $invest = Invest::get($invest_id);
        $project= $invest->getProject();
        $reward = $this->validate($project_id, null, $_dummy, $invest);

        if($reward instanceOf Response) return $reward;

        if(!in_array($invest->status, [Invest::STATUS_CHARGED, Invest::STATUS_PAID])) {
            Message::error(Text::get('project-invest-fail'));
            return $this->redirect('/invest/' . $project_id . '/payment?' . $this->query);
        }

        $lsuf = (LANG != 'es') ? '?lang='.LANG : '';
        $URL = $request->getSchemeAndHttpHost();
        $url = $URL . '/widget/project/' . $project_id;
        $widget_code = Text::widget($url . $lsuf);
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

        if (!Config::isMasterNode())
            $share_title = str_replace ('#goteo', '#'.strtolower (NODE_NAME), $share_title);

        $share_url = $URL . '/project/'.$project_id;
        $facebook_url = 'https://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($share_title);
        $twitter_url = 'https://twitter.com/intent/tweet?text=' . urlencode($share_title . ': ' . $share_url);

        if ($reward instanceOf Response) return $reward;

        return $this->viewResponse('invest/share',[
            'facebook_url' => $facebook_url,
            'twitter_url' => $twitter_url,
            'widget_code' => $widget_code,
            'widget_code_investor' => $widget_code,
            'step' => 4
        ]);
    }

    // Send a public support message
    public function supportMsgAction(Request $request) {
        if ($request->isMethod('post')) {
            $msg = $request->request->get('msg');
            $invest = $request->request->get('invest');
            if(empty($msg))
                $result=false;
            else
                $result=Invest::newSupportMessage($invest, $msg);
        }

        return $this->jsonResponse(['result' => $result]);
    }

}
