<?php

namespace Omnipay\Stripe\Subscription\Message;

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

    /**
     * @param array{user: User, invest: Invest} $data
     */
    public function sendData($data)
    {
        $user = $data['user'];
        $invest = $data['invest'];

        $project = $invest->getProject();

        $price = $this->stripe->prices->create([
            'unit_amount' => ($invest->amount + $invest->donate_amount) * 100,
            'currency' => 'eur',
            'recurring' => ['interval' => 'month'],
            'product' => $this->getStripeProduct($invest)->id
        ]);

        $session = $this->stripe->checkout->sessions->create([
            'customer' => $this->getStripeCustomer($data['user'])->id,
            'success_url' => sprintf('%s?session_id={CHECKOUT_SESSION_ID}', $this->getRedirectUrl(
                'invest',
                $project->id,
                $invest->id,
                'complete'
            )),
            'cancel_url' => $this->getRedirectUrl(
                'project',
                $project->id
            ),
            'mode' => 'subscription',
            'line_items' => [
                [
                    'price' => $price->id,
                    'quantity' => 1
                ]
            ],
            'metadata' => [
                'project' => $project->id,
                'reward' => $this->getInvestReward($invest, ''),
                'user' => $user->id,
            ]
        ]);

        return new SubscriptionResponse($this, $session->id);
    }

    public function completePurchase(array $options = [])
    {
        $session = $this->stripe->checkout->sessions->retrieve($_REQUEST['session_id']);

        $this->stripe->subscriptions->update(
            $session->subscription, [
            'metadata' => $session->metadata->toArray()
        ]);

        return new SubscriptionResponse($this, $session->id);
    }

    private function getRedirectUrl(...$args): string
    {
        return sprintf(
            '%s://%s/%s',
            isset($_SERVER['HTTPS']) ? 'https' : 'http',
            $_SERVER['HTTP_HOST'],
            implode('/', $args)
        );
    }

    /**
     * Get the ID of the reward for the invest, or a given string in case of no reward
     * @param Invest $invest
     * @param string $noReward The string to return in case of no reward selected
     * @return string
     */
    private function getInvestReward(Invest $invest, string $noReward): string
    {
        return !empty($invest->getRewards())
            ? $invest->getRewards()[0]->id
            : $noReward;
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
        /** @var User */
        $user = $invest->getUser();

        /** @var Project */
        $project = $invest->getProject();

        $productId = sprintf(
            '%s_%s_%s',
            $project->id,
            $this->getInvestReward($invest, 'noreward'),
            $user->id,
        );

        try {
            return $this->stripe->products->retrieve($productId);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $productDescription = sprintf(
                '%s - %s',
                $project->name,
                $this->getInvestReward($invest, Text::get('invest-resign'))
            );

            return $this->stripe->products->create([
                'id' => $productId,
                'name' => $productDescription,
                'description' => $productDescription
            ]);
        }
    }
}
