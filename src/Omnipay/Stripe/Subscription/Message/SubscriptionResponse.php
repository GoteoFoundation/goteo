<?php

namespace Omnipay\Stripe\Subscription\Message;

use Goteo\Application\Config;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Stripe\Checkout\Session as StripeSession;
use Stripe\StripeClient;

class SubscriptionResponse extends AbstractResponse implements RedirectResponseInterface
{
    private StripeClient $stripe;

    private StripeSession $checkout;

    public function __construct(RequestInterface $request, string $checkoutSessionId)
    {
        parent::__construct($request, $checkoutSessionId);

        $this->stripe = new StripeClient(Config::get('payments.stripe.secretKey'));
        $this->checkout = $this->stripe->checkout->sessions->retrieve($checkoutSessionId);
    }

    public function isSuccessful()
    {
        return $this->checkout->status === StripeSession::STATUS_COMPLETE;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->checkout->url;
    }
}
