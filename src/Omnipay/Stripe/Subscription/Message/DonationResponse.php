<?php

namespace Omnipay\Stripe\Subscription\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Stripe\Checkout\Session as StripeSession;

class DonationResponse extends AbstractResponse implements RedirectResponseInterface
{
    private StripeSession $checkout;

    public function __construct(RequestInterface $request, StripeSession $checkout)
    {
        parent::__construct($request, ['checkout' => $checkout]);

        $this->checkout = $checkout;
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
