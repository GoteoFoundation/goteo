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
use Goteo\Application\Currency;

/**
 * Creates a Payment Method that uses Paypal provider
 */
class PaypalPaymentMethod extends AbstractPaymentMethod {

    public function getGatewayName() {
        return 'PayPal_Express';
    }

    public function purchase() {
        // Let's obtain the gateway and the
        $gateway = $this->getGateway();

        // You can specify your paypal gateway details in config/settings.yml
        if(!$gateway->getLogoImageUrl()) $gateway->setLogoImageUrl(SRC_URL . '/goteo_logo.png');

        return parent::purchase();

    }

    public function completePurchase() {
        // Let's obtain the gateway and the
        $gateway = $this->getGateway();
        $invest = $this->getInvest();
        $gateway->setCurrency(Currency::getDefault('id'));
        $payment = $gateway->completePurchase([
                    'amount' => (float) $invest->amount,
                    'description' => $this->getInvestDescription(),
                    'clientIp' => $this->getRequest()->getClientIp(),
                    'returnUrl' => $this->getCompleteUrl(),
                    'cancelUrl' => $this->getCompleteUrl(),
        ]);


        // Additional Invest details
        $invest->setAccount($payment->getPayerID() ? $payment->getPayerID() : $payment->getData()['PAYERID']);
        $invest->setPayment($payment->getToken() ? $payment->getToken() : $payment->getData()['TOKEN']);


        return $payment->send();

    }

}
