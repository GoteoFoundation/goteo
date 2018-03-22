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

/**
 * Payment methods in Goteo must implent this interface
 *
 */
interface PaymentMethodInterface {
    /**
     * Contructor should have the Goteo user
     * @param User $user [description]
     */
    public function __construct(\Goteo\Model\User $user);

    /**
     * Returns the id of the method (max 20 chars long)
     * @return string id of the method
     */
    static public function getId();

    /**
     * Returns the name of the payment method (a sort description)
     * @return string name of the method
     */
    public function getName();

    /**
     * Returns a short description of the method
     * @return string description of the method
     */
    public function getDesc();

    /**
     * Returns a icon for the method
     * @return string URL of the icon
     */
    public function getIcon();

    /**
     * Should return if method must be registered but in a inactive state
     * so it can be shown on the payment page as a temporary non-available method
     * @param integer $amount The method can decide to be active depending on the amount
     * @return boolean status
     */
    public function isActive($amount = 0);

    /**
     * Returns if the payment method is public or not.
     * Non-public methods can be used for custom payments outside the user-scope
     * and will not be shown in the payment page
     * @param integer $amount The method can decide to be active depending on the amount
     * @return boolean status
     */
    public function isPublic($amount = 0);

    /**
     * Sets the Invest in order to be able to create a proper gateway request
     * @param Invest $invest Invest object
     */
    public function setInvest(\Goteo\Model\Invest $invest);

    /**
     * Gets the Invest object
     * @return Invest $invest Invest object
     */
    public function getInvest();

    /**
     * Sets the Request in order to be able to create a proper gateway request
     * @param Request $request Symfony HttpFoundation Request object
     */
    public function setRequest(\Symfony\Component\HttpFoundation\Request $request);

    /**
     * This method gives the change to change the Response where to redirect after a $method->completePurchase() situation
     * @param  RedirectResponseInterface $response
     * @return Response|null             A valid Symfony Response or null
     */
    public function getDefaultHttpResponse(\Omnipay\Common\Message\ResponseInterface $response);

    /**
     * Gets the current Request
     * @return Request $request Symfony HttpFoundation Request object
     */
    public function getRequest();

    /**
     * Starts the purchase action
     * Called when user pushes the button "pay"
     * @return Ommnipay\Common\Message\ResponseInterface Omnipay Response Object
     */
    public function purchase();

    /**
     * Ends the purchase action
     * Called when the user returns from the payment gateway or the gateway notifies via notifyUrl
     * @return Ommnipay\Common\Message\ResponseInterface Omnipay Response Object
     */
    public function completePurchase();

    /**
     * Returns if the gateway can refund a investion
     * @return Boolean true or false
     */
    public function refundable();

    /**
     * Processes a refund action on the gateway
     * @return Ommnipay\Common\Message\ResponseInterface Omnipay Response Object
     */
    public function refund();

    /**
     * Calculates banks fee in a generic way, based on settings.yml config and following the Paypal fees rules (which suits many gateways)
     * @return float
     */
    static public function calculateComission($total_invests, $total_amount, $returned_invests = 0, $returned_amount = 0);

    /**
     * Internal payments does not increased raised amounts
     * (pool)
     * @return boolean
     */
    static public function isInternal();
}
