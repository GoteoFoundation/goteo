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

use Goteo\Application\Config;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Payment\PaymentException;
use Goteo\Library\Text;
use Goteo\Library\Currency;

use Symfony\Component\HttpFoundation\Request;
use Omnipay\Omnipay;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Helper class with common of the interface methods implemented in a simple way
 *
 * Payments using this implementation use the Omnipay library:
 * http://omnipay.thephpleague.com/
 */
abstract class AbstractPaymentMethod implements PaymentMethodInterface {
    protected $gateway;
    protected $invest;
    protected $request;
    protected $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * Returns the id of the method (max 20 chars long)
     * @return string id of the method
     */
    static public function getId() {
        $parts = explode('\\', get_called_class());
        $c = end($parts);
        $c = strtolower(str_replace('PaymentMethod', '', $c));
        if(empty($c)) {
            throw new PaymentException('Method getId() must return a valid string');
        }
        return $c;
    }

    /**
     * Returns the name of the payment method (a sort description)
     * @return string name of the method
     */
    public function getName() {
        return Text::get('invest-' . $this::getId() . '-method');
    }

    /**
     * Returns a short description of the method
     * @return string description of the method
     */
    public function getDesc() {
        return $this->getName();
    }

    /**
     * Returns a icon for the method
     * @return string URL of the icon
     */
    public function getIcon() {
        // $this->getGateway()->getLogoImageUrl();
        return SRC_URL . '/assets/img/pay/' . $this::getId() . '.png';
    }

    /**
     * Should return if method must be registered but in a inactive state
     * so it can be shown on the payment page as a temporary non-available method
     * @return boolean status
     */
    public function isActive($amount = 0) {
        return true;
    }

    /**
     * Sets the Invest in order to be able to create a proper gateway request
     * @param Invest $invest Invest object
     */
    public function setInvest(Invest $invest) {
        $this->invest = $invest;
        return $this;
    }

    /**
     * Gets the Invest object
     * @return Invest $invest Invest object
     */
    public function getInvest() {
        return $this->invest;
    }

    /**
     * Sets the Request in order to be able to create a proper gateway request
     * @param Request $request Symfony HttpFoundation Request object
     */
    public function setRequest(Request $request) {
        $this->request = $request;
        return $this;
    }

    /**
     * Gets the current Request
     * @return Request $request Symfony HttpFoundation Request object
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * This method gives the change to change the Response where to redirect after a $method->completePurchase() situation
     * @param  RedirectResponseInterface $response
     * @return Response|null             A valid Symfony Response or null
     */
    public function getDefaultHttpResponse(ResponseInterface $response) {
        return null;
    }
    /**
     * Starts the purchase action
     * Called when user pushes the button "pay"
     * @return Ommnipay\Common\Message\ResponseInterface Omnipay Response Object
     */
    public function purchase() {
        // Let's obtain the gateway and the
        $gateway = $this->getGateway();
        $gateway->setCurrency(Currency::DEFAULT_CURRENCY);
        return $gateway->purchase([
                    'amount' => (float) $this->getInvest()->amount,
                    'description' => $this->getInvestDescription(),
                    'returnUrl' => $this->getCompleteUrl(),
                    'cancelUrl' => $this->getCompleteUrl(),
        ])->send();
    }

    /**
     * Ends the purchase action
     * Called when the user returns from the payment gateway or the gateway notifies via notifyUrl
     * @return Ommnipay\Common\Message\ResponseInterface Omnipay Response Object
     */
    public function completePurchase() {
        // Let's obtain the gateway and the
        $gateway = $this->getGateway();
        return $gateway->completePurchase([
                    'amount' => (float) $this->getInvest()->amount,
                    'description' => $this->getInvestDescription(),
                    'clientIp' => $this->getRequest()->getClientIp(),
                    'returnUrl' => $this->getCompleteUrl(),
                    'cancelUrl' => $this->getCompleteUrl(),
        ])->send();

    }

    public function getCompleteUrl() {
        $request = $this->getRequest();
        $invest = $this->getInvest();
        return $request->getSchemeAndHttpHost() . '/invest/' . $invest->project . '/' . $invest->id . '/complete';
    }
    /**
     * Returns a description for the invest
     * @param  Invest $invest [description]
     * @return [type]         [description]
     */
    public function getInvestDescription() {
        $invest = $this->getInvest();
        $project = $invest->getProject();
        $msg = ''; // TODO: from Text::get()
        if($reward = $invest->getFirstReward()) {
            $msg = $reward->reward . " - ";
        }
        $msg .= $project->name;
        return $msg;
    }

    /**
     * This must provide a valid Omnipay Gateway name
     * @return string The name of the Omnipay gateway
     */
    public function getGatewayName() {
        return ucfirst($this::getId());
    }

    /**
     * The most important function here
     *
     * It must return the result of the Ommnipay::create() function
     *
     * @return Omnipay\Common\GatewayInterface a Omnipay valid object
     */
    public function getGateway() {
        if(!$this->gateway) {
            $this->gateway = Omnipay::create($this->getGatewayName());
            if(!in_array('Omnipay\Common\GatewayInterface', class_implements($this->gateway))) {
                throw new PaymentException("Error on retrieving Omnipay Gateway Class. It must implement Omnipay\Common\GatewayInterface!");
            }

            foreach($this->gateway->getDefaultParameters() as $var => $val) {
                $config = Config::get('payments.' . $this::getId() . '.' . $var);
                $method = "set" . ucfirst($var);
                if($config && method_exists($this->gateway, $method)) {
                    $this->gateway->$method($config);
                }
            }
        }
        return $this->gateway;
    }

}
