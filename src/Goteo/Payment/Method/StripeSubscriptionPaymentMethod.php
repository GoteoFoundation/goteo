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
use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Payment\PaymentException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Omnipay;
use Omnipay\Stripe\SubscriptionGateway;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;

class StripeSubscriptionPaymentMethod implements PaymentMethodInterface
{
    const PAYMENT_METHOD_ID = "stripe";

    private User $user;
    private Invest $invest;
    private Request $request;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    static public function getId(): string
    {
        return self::PAYMENT_METHOD_ID;
    }

    public function getIdNonStatic(): string
    {
        return self::PAYMENT_METHOD_ID;
    }

    public function getName(): string
    {
        return Text::get('invest-stripe-method');
    }

    public function getDesc(): string
    {
        return $this->getName();
    }

    public function getIcon(): string
    {
        return SRC_URL . '/assets/img/pay/' . $this->getIdNonStatic() . '.png';
    }

    public function isActive($amount = 0): bool
    {
        return true;
    }

    public function isPublic($amount = 0): bool
    {
        return true;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setInvest(Invest $invest)
    {
        $this->invest = $invest;

        return $this;
    }

    public function getInvest()
    {
        return $this->invest;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getDefaultHttpResponse(ResponseInterface $response)
    {
        return null;
    }

    public function purchase()
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

    public function completePurchase()
    {
        return;
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
