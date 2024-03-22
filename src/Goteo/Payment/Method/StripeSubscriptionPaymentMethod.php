<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Payment\Method;

use Goteo\Library\Text;
use Goteo\Payment\PaymentException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Omnipay;
use Omnipay\Stripe\Subscription\Gateway as SubscriptionGateway;
use Stripe\Subscription;

class StripeSubscriptionPaymentMethod extends AbstractPaymentMethod
{
    const PAYMENT_METHOD_ID = "stripe_subscription";

    static public function getId(): string
    {
        return self::PAYMENT_METHOD_ID;
    }

    public function getIdNonStatic(): string
    {
        return self::PAYMENT_METHOD_ID;
    }

    public function getGatewayName(): string
    {
        return 'Stripe\\Subscription\\';
    }

    public function getName(): string
    {
        return Text::get('invest-stripe-method');
    }

    public function getDesc(): string
    {
        return Text::get('invest-stripe-description');
    }

    public function getIcon(): string
    {
        return SRC_URL . '/assets/img/pay/stripe.png';
    }

    public function isActive($amount = 0): bool
    {
        return true;
    }

    public function isPublic($amount = 0): bool
    {
        return true;
    }

    public function getGateway(): SubscriptionGateway
    {
        return Omnipay::create(SubscriptionGateway::class);
    }

    public function purchase(): ResponseInterface
    {
        return $this->getGateway()->purchase([
            'invest' => $this->invest,
            'user' => $this->user
        ])->send();
    }

    public function completePurchase(): ResponseInterface
    {
        $response = $this->getGateway()->completePurchase();

        /** @var Subscription */
        $subscription = $response->getData()['subscription'];

        $this->invest->setPayment($subscription->id);

        return $response;
    }

    public function refundable(): bool
    {
        return false;
    }

    public function refund(): ResponseInterface
    {
        throw new PaymentException("Refund not yet supported for subscriptions", 1);
    }

    public function isInternal(): bool
    {
        return false;
    }

    public function isSubscription(): bool
    {
        return true;
    }
}
