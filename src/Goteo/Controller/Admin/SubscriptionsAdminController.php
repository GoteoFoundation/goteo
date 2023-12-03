<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Config\ConfigException;
use Goteo\Application\Message;
use Goteo\Model\Project\Reward;
use Goteo\Model\User;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;

class SubscriptionsAdminController extends AbstractAdminController
{
    protected static $icon = '<i class="fa fa-rss"></i>';

    private static StripeClient $stripe;

    public function __construct()
    {
        try {
            $stripeKey = Config::get('payments.stripe.secretKey');
        } catch (ConfigException $e) {
            throw new \RuntimeException('Stripe key not configured');
        }

        $this->stripe = new StripeClient($stripeKey);
    }

    public static function getGroup(): string
    {
        return 'activity';
    }

    public static function getRoutes(): array
    {
        return [
            new Route(
                '/',
                ['_controller' => __CLASS__ . "::listAction"]
            )
        ];
    }

    public function listAction(Request $request): Response
    {
        $subscriptions = [];

        try {
            $subscriptions = $this->stripe->subscriptions->all(['status' => 'all', 'expand' => ['data.customer', 'data.plan.product'], 'test_clock' => 'clock_1OD3G0FmqwVqTUhDAwOHYwmw'])->toArray();
        } catch (ApiErrorException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin');
        }

        $subscriptions = $this->treatSubscriptions($subscriptions['data']);
        return $this->viewResponse('admin/subscriptions/list', [
            'subscriptions' => $subscriptions
        ]);
    }

    private function treatSubscriptions(array $subscriptions = []): array
    {
        foreach($subscriptions as $index => $subscription) {
            $subscriptions[$index]['user'] = User::getByEmail($subscription['customer']['email']);
            list($projectId, $rewardId) = explode('_', $subscription['items']['data'][0]['price']['product']);
            $subscriptions[$index]['reward'] = Reward::get($rewardId);
        }

        return $subscriptions;
    }
}
