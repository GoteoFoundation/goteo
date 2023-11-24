<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Dashboard;

use Goteo\Application\Config;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Library\Forms\Model\UserSubscriptionForm;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Stripe\Exception\ExceptionInterface;
use Stripe\StripeClient;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;

class SubscriptionDashboardController extends Controller
{
    private StripeClient $stripe;

    public function __construct()
    {
        View::setTheme('responsive');
        $this->user = Session::getUser();
        if (!$this->user) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        $this->stripe = new StripeClient(Config::get('payments.stripe.secretKey'));
    }

    public static function createSidebar($section, $zone = '')
    {
        $user = Session::getUser();
        //$total_messages = Comment::getUserThreads($user, [], 0, 0, true);
        //$total_mails = Mail::getSentList(['user' => $user->email, 'message' => false], 0, 0, true);
        $total_invests = Invest::getList(['users' => $user, 'status' => Invest::$RAISED_STATUSES], null, 0, 0, 'total');

        if ($section === 'activity') {
            Session::addToSidebarMenu('<i class="icon icon-2x icon-activity"></i> ' . Text::get('dashboard-menu-activity'), '/dashboard/activity', 'activity');
            Session::addToSidebarMenu('<i class="fa fa-2x fa-gift"></i> ' . Text::get('dashboard-rewards-my-invests'), '/dashboard/rewards', 'rewards');
            Session::addToSidebarMenu('<i class="fa fa-2x fa-ticket"></i> ' . Text::get('dashboard-rewards-my-subscriptions'), '/dashboard/subscriptions', 'subscriptions');
            Session::addToSidebarMenu('<i class="icon icon-2x icon-partners"></i> ' . Text::get('regular-messages') . ' <span class="badge">' . $total_messages . '</span>', '/dashboard/messages', 'messages');
            Session::addToSidebarMenu('<i class="fa fa-2x fa-envelope"></i> ' . Text::get('dashboard-mail-mailing'), '/dashboard/mailing', 'mailling');
        }
        if ($section === 'wallet') {
            Session::addToSidebarMenu('<i class="icon icon-2x icon-wallet-sidebar"></i> ' . Text::get('dashboard-menu-pool'), '/dashboard/wallet', 'wallet');
            Session::addToSidebarMenu('<i class="fa fa-2x fa-fw fa-download"></i> ' . Text::get('recharge-button'), '/pool', 'recharge');

            if (Config::get('donate.dashboard')) {
                Session::addToSidebarMenu('<i class="icon icon-2x fa-fw icon-save-the-world"></i> ' . Text::get('donate-button'), '/donate/select', 'donate');
            }
        }
        View::getEngine()->useData([
            'zone' => $zone,
            'section' => $section,
            'total_messages' => $total_messages
        ]);
    }

    private function getSubscriptionData(Subscription $subscription)
    {
        return [
            'subscription' => $subscription->toArray(),
            'product' => $this->stripe->products->retrieve($subscription->plan->product)->toArray()
        ];
    }

    public function viewAction($sid = null)
    {
        $subscription = $this->stripe->subscriptions->retrieve($sid);

        self::createSidebar('activity', 'subscriptions');
        return $this->viewResponse('dashboard/subscriptions/subscription', [
            'section' => 'activity',
            'subscription' => $this->getSubscriptionData($subscription)
        ]);
    }

    public function cancelAction($sid = null, Request $request)
    {
        $subscription = $this->stripe->subscriptions->retrieve($sid);
        $processor = $this->getModelForm(UserSubscriptionForm::class, $this->user, [], [], $request);

        $processor->createForm();
        $form = $processor->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isMethod('post')) {
            try {
                $this->stripe->subscriptions->cancel($sid);

                Message::info(Text::get('subscription-cancel-saved'));
            } catch (ExceptionInterface $e) {
                Message::error($e->getMessage());
            }
        }

        self::createSidebar('activity', 'subscriptions');
        return $this->viewResponse('dashboard/subscriptions/cancel', [
            'section' => 'activity',
            'form' => $form->createView(),
            'subscritpion' => $this->getSubscriptionData($subscription)
        ]);
    }
}
