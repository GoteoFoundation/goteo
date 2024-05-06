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

use Goteo\Model\Invest;
use Goteo\Model\User;
use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Payment methods in Goteo must implement this interface
 */
interface PaymentMethodInterface {

    public function __construct(User $user);

    /**
     * @deprecated Use the non-static method getIdNonStatic()
     * Returns the id of the method (max 20 chars long)
     */
    static public function getId(): string;

    public function getIdNonStatic(): string;

    /**
     * Returns the name of the payment method (a sort description)
     */
    public function getName(): string;

    /**
     * Returns a short description of the method
     */
    public function getDesc(): string;

    /**
     * Returns a icon for the method
     */
    public function getIcon(): string;

    /**
     * Should return if method must be registered but in a inactive state
     * so it can be shown on the payment page as a temporary non-available method
     * @param integer $amount The method can decide to be active depending on the amount
     */
    public function isActive($amount = 0): bool;

    /**
     * Returns if the payment method is public or not.
     * Non-public methods can be used for custom payments outside the user-scope
     * and will not be shown in the payment page
     * @param integer $amount The method can decide to be active depending on the amount
     */
    public function isPublic($amount = 0): bool;

    /**
     * Sets the Invest in order to be able to create a proper gateway request
     * @param Invest $invest Invest object
     */
    public function setInvest(Invest $invest);

    /**
     * Gets the Invest object
     * @return Invest $invest Invest object
     */
    public function getInvest();

    /**
     * Sets the Request in order to be able to create a proper gateway request
     * @param Request $request Symfony HttpFoundation Request object
     */
    public function setRequest(Request $request);

    /**
     * This method gives the change to change the Response where to redirect after a $method->completePurchase() situation
     * @param  ResponseInterface $response
     * @return Response|null             A valid Symfony Response or null
     */
    public function getDefaultHttpResponse(ResponseInterface $response);

    /**
     * Gets the current Request
     * @return Request $request Symfony HttpFoundation Request object
     */
    public function getRequest();

    /**
     * Starts the purchase action
     * Called when user pushes the button "pay"
     * @return ResponseInterface Omnipay Response Object
     */
    public function purchase();

    /**
     * Ends the purchase action
     * Called when the user returns from the payment gateway or the gateway notifies via notifyUrl
     * @return ResponseInterface Omnipay Response Object
     */
    public function completePurchase();

    /**
     * Returns if the gateway can refund an investment
     */
    public function refundable(): bool;

    /**
     * Processes a refund action on the gateway
     */
    public function refund(): ResponseInterface;

    /**
     * Calculates banks fee in a generic way, based on settings.yml config and following the Paypal fees rules (which suits many gateways)
     * @return float
     */
    public function calculateCommission($total_invests, $total_amount, $returned_invests = 0, $returned_amount = 0);

    /**
     * Internal payments does not increased raised amounts
     * (pool)
     */
    public function isInternal(): bool;

    /**
     * Subscription payments are charged recurrently
     */
    public function isSubscription(): bool;
}
