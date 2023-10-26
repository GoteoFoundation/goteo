<?php

namespace Omnipay\Stripe\Message;

use Goteo\Application\Config;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Omnipay\Common\Message\AbstractRequest;
use Stripe\Customer;
use Stripe\Product;
use Stripe\StripeClient;

class SubscriptionRequest extends AbstractRequest
{
    private array $data;
    private StripeClient $stripe;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->stripe = new StripeClient(Config::get('payments.stripe.secretKey'));
    }

    public function getData()
    {
        return $this->data;
    }

    public function sendData($data)
    {
        $price = $this->stripe->prices->create([
            'unit_amount' => ($data['invest']->amount + $data['invest']->donate_amount) * 100,
            'currency' => 'eur',
            'recurring' => ['interval' => 'month'],
            'product' => $this->getStripeProduct($data['invest'])->id
        ]);

        $subscription = $this->stripe->subscriptions->create([
            'customer' => $this->getStripeCustomer($data['user'])->id,
            'items' => [
                ['price' => $price->id]
            ],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
            'expand' => ['latest_invoice.payment_intent']
        ]);

        return new SubscriptionResponse($this, $subscription);
    }

    private function getStripeCustomer(User $user): Customer
    {
        $search = $this->stripe->customers->search([
            'query' => sprintf('email:\'%s\'', $user->email)
        ]);

        if (!empty($search['data'])) {
            return $search['data'][0];
        }

        return $this->stripe->customers->create([
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    private function getStripeProduct(Invest $invest): Product
    {
        /** @var Project */
        $project = $invest->getProject();

        $productId = sprintf(
            '%s_%s',
            $project->id,
            $invest->getFirstReward() ? $invest->getFirstReward()->id : 0
        );

        try {
            return $this->stripe->products->retrieve($productId);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $productDescription = sprintf(
                '%s - %s',
                $project->name,
                $invest->getFirstReward() ? $invest->getFirstReward()->reward : Text::get('invest-resign')
            );

            return $this->stripe->products->create([
                'id' => $productId,
                'name' => $project->name,
                'description' => $productDescription
            ]);
        }
    }
}
