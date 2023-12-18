<?php

namespace Omnipay\Stripe\Subscription\Message;

use Goteo\Application\Config;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\Project;
use Goteo\Model\User;
use Omnipay\Common\Message\AbstractRequest;
use Stripe\Checkout\Session as CheckoutSession;
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

        /** @var Project */
        $project = $invest->getProject();

        $customer = $this->getStripeCustomer($user)->id;
        $metadata = $this->getMetadata($project, $invest, $user);

        $successUrl = sprintf('%s?session_id={CHECKOUT_SESSION_ID}', $this->getRedirectUrl(
            'invest',
            $project->id,
            $invest->id,
            'complete'
        ));

        $session = $this->stripe->checkout->sessions->create([
            'customer' => $customer,
            'success_url' => $successUrl,
            'cancel_url' => $this->getRedirectUrl('project', $project->id),
            'mode' => CheckoutSession::MODE_SUBSCRIPTION,
            'line_items' => [
                [
                    'price' => $this->stripe->prices->create([
                        'unit_amount' => $invest->amount * 100,
                        'currency' => $project->currency,
                        'recurring' => ['interval' => 'month'],
                        'product' => $this->getStripeProduct($invest)->id
                    ])->id,
                    'quantity' => 1
                ]
            ],
            'metadata' => $metadata
        ]);

        return new SubscriptionResponse($this, $session->id);
    }

    public function completePurchase(array $options = [])
    {
        // Dirty sanitization because something is double concatenating the ?session_id query param
        $sessionId = explode('?', $_REQUEST['session_id'])[0];
        $session = $this->stripe->checkout->sessions->retrieve($sessionId);
        $metadata = $session->metadata->toArray();

        if ($session->subscription) {
            $this->stripe->subscriptions->update(
                $session->subscription,
                [
                    'metadata' => $metadata
                ]
            );
    
            if ($metadata['donate_amount'] < 1) {
                return new SubscriptionResponse($this, $session->id);
            }

            $donation = $this->stripe->checkout->sessions->create([
                'customer' => $this->getStripeCustomer(User::get($metadata['user']))->id,
                'success_url' => sprintf('%s?session_id={CHECKOUT_SESSION_ID}', $this->getRedirectUrl(
                    'invest',
                    $metadata['project'],
                    $metadata['invest'],
                    'complete'
                )),
                'cancel_url' => $this->getRedirectUrl('project', $metadata['project']->id),
                'mode' => CheckoutSession::MODE_PAYMENT,
                'line_items' => [
                    [
                        'price' => $this->stripe->prices->create([
                            'unit_amount' => $metadata['donate_amount'] * 100,
                            'currency' => Config::get('currency'),
                            'product_data' => [
                                'name' => Text::get('donate-meta-description')
                            ]
                        ])->id,
                        'quantity' => 1
                    ]
                ],
                'metadata' => $metadata
            ]);
    
            return new DonationResponse($this, $donation->id);
        }

        if ($session->payment_intent) {
            return new SubscriptionResponse($this, $session->id);
        }
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

    private function getMetadata(Project $project, Invest $invest, User $user): array
    {
        return [
            'donate_amount' => $invest->donate_amount,
            'project' => $project->id,
            'invest' => $invest->id,
            'reward' => $this->getInvestReward($invest, ''),
            'user' => $user->id,
        ];
    }
}
