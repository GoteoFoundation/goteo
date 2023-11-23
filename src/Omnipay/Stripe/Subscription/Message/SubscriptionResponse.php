<?php

namespace Omnipay\Stripe\Subscription\Message;

use Omnipay\Common\Message\AbstractResponse;
use Stripe\Subscription;

class SubscriptionResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return $this->data instanceof Subscription;
    }
}
