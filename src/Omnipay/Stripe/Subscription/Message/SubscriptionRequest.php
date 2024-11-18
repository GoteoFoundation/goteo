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

        $customer = $this->getStripeCustomer($user)->id;
        $metadata = $this->getMetadata($invest);

        $successUrl = $this->getRedirectUrl('pool', $invest->id, 'complete');
        if ($invest->getProject()) {
            $successUrl = $this->getRedirectUrl(
                'invest',
                $metadata['project'],
                $invest->id,
                'complete'
            );
        }

        $redirectUrl = $this->getRedirectUrl('dashboard', 'wallet');
        if ($invest->getProject()) {
            $redirectUrl = $this->getRedirectUrl('project', $metadata['project']);
        }

        $checkout = $this->stripe->checkout->sessions->create([
            'customer' => $customer,
            'success_url' => sprintf('%s?session_id={CHECKOUT_SESSION_ID}', $successUrl),
            'cancel_url' => $redirectUrl,
            'mode' => CheckoutSession::MODE_SUBSCRIPTION,
            'line_items' => [
                [
                    'price' => $this->stripe->prices->create([
                        'unit_amount' => $invest->amount * 100,
                        'currency' => $this->getStripeCurrency($invest, $user),
                        'recurring' => ['interval' => 'month'],
                        'product' => $this->getStripeProduct($invest)->id,
                        'metadata' => $metadata
                    ])->id,
                    'quantity' => 1
                ]
            ],
            'metadata' => $metadata
        ]);

        return new SubscriptionResponse($this, $checkout);
    }

    public function completePurchase(array $options = [])
    {
        // Dirty sanitization because something is double concatenating the ?session_id query param
        $sessionId = explode('?', $_REQUEST['session_id'])[0];
        $checkout = $this->stripe->checkout->sessions->retrieve($sessionId);
        $metadata = $checkout->metadata->toArray();

        if (!$checkout->subscription) {
            throw new \Exception("Could not retrieve Subscription from Stripe after checkout");
        }

        $subscription = $this->stripe->subscriptions->retrieve($checkout->subscription);
        $this->stripe->subscriptions->update(
            $checkout->subscription,
            [
                'metadata' => $metadata
            ]
        );

        if ($metadata['donate_amount'] < 1) {
            return new SubscriptionResponse($this, $checkout, $subscription);
        }

        $successUrl = $this->getRedirectUrl('pool', $metadata['invest']);
        if ($metadata['project'] !== '') {
            $successUrl = $this->getRedirectUrl(
                'invest',
                $metadata['project'],
                $metadata['invest'],
                'complete'
            );
        }

        $cancelUrl = $this->getRedirectUrl('dashboard', 'wallet');
        if ($metadata['project'] !== '') {
            $cancelUrl = $this->getRedirectUrl('project', $metadata['project']);
        }

        $donationCheckout = $this->stripe->checkout->sessions->create([
            'customer' => $this->getStripeCustomer(User::get($metadata['user']))->id,
            'success_url' => sprintf('%s?session_id={CHECKOUT_SESSION_ID}', $successUrl),
            'cancel_url' => $cancelUrl,
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

        return new DonationResponse($this, $donationCheckout);
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
        $productId = $this->getProductId($invest);

        try {
            return $this->stripe->products->retrieve($productId);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            if ($project = $invest->getProject()) {
                $productDescription = sprintf(
                    '%s - %s',
                    $project->name,
                    $this->getInvestReward($invest, Text::get('invest-resign'))
                );
            } else {
                $productDescription = Text::get('invest-pool-method');
            }


            return $this->stripe->products->create([
                'id' => $productId,
                'name' => $productDescription,
                'description' => $productDescription
            ]);
        }
    }

    private function getMetadata(Invest $invest): array
    {
        /** @var Project */
        $project = $invest->getProject();
        /** @var User */
        $user = $invest->getUser();

        $projectId = ($project) ? $project->id : null;

        return [
            'donate_amount' => $invest->donate_amount,
            'project' => $projectId,
            'invest' => $invest->id,
            'reward' => $this->getInvestReward($invest, ''),
            'user' => $user->id,
        ];
    }

    private function getProductId(Invest $invest): string
    {
        if ($project = $invest->getProject())
            return $this->getProductWithProjectId($invest, $project);

        return $this->getProductWithoutProjectId($invest);
    }

    private function getProductWithProjectId(Invest $invest, Project $project): string
    {
        /** @var User */
        $user = $invest->getUser();

        return sprintf(
            '%s_%s_%s',
            $project->id,
            $this->getInvestReward($invest, 'noreward'),
            $user->id,
        );
    }

    private function getProductWithoutProjectId(Invest $invest): string
    {
        /** @var User */
        $user = $invest->getUser();

        return sprintf(
            '%s_%s',
            $invest->id,
            $user->id
        );
    }

    private function getStripeCurrency(Invest $invest, User $user): string
    {
        if ($project = $invest->getProject()) {
            return $project->currency;
        }

        /** @var stdClass */
        $preferences = User::getPreferences($user);

        if (\property_exists($preferences, 'currency')) {
            return $preferences->currency;
        }

        return Config::get('currency');
    }
}
