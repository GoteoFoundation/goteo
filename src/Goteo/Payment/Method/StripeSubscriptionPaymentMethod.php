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
    const PAYMENT_METHOD_ID = "stripe";

    static public function getId(): string
    {
        return self::PAYMENT_METHOD_ID;
    }

    public function getIdNonStatic(): string
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

    public function purchase(): ResponseInterface
    {
        $gateway = Omnipay::create(SubscriptionGateway::class);

        $response = $gateway->purchase([
            'invest' => $this->invest,
            'user' => $this->user
        ])->send();

        /** @var Subscription */
        $subscription = $response->getData();

        $this->invest->setPreapproval($subscription->id);

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

    public function calculateCommission($total_invests, $total_amount, $returned_invests = 0, $returned_amount = 0)
    {
        // Confirm Stripe commission
    }

    public function isInternal(): bool
    {
        return false;
    }
}
