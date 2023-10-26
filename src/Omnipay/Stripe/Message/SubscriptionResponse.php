<?php

namespace Omnipay\Stripe\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Stripe\Subscription;

class SubscriptionResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return $this->data instanceof Subscription;
    }
}
