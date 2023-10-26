<?php

namespace Omnipay\Stripe;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Stripe\Message\SubscriptionRequest;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class SubscriptionGateway extends AbstractGateway
{
    /**
     * Create a new gateway instance
     *
     * @param ClientInterface          $httpClient  A HTTP client to make API calls with
     * @param HttpRequest     $httpRequest A Symfony HTTP request object
     */
    public function __construct(ClientInterface $httpClient = null, HttpRequest $httpRequest = null)
    {
        parent::__construct($httpClient, $httpRequest);
    }

    public function getName()
    {
        return 'Stripe Subscriptions';
    }

    public function purchase($options = array())
    {
        return new SubscriptionRequest($options);
    }
}
