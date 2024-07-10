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

use Goteo\Application\Currency;
use Goteo\Model\Project;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\PayPal\ExpressGateway;

class PaypalPaymentMethod extends AbstractPaymentMethod
{

    public function getGatewayName(): string
    {
        return 'PayPal_Express';
    }

    public function purchase(): ResponseInterface
    {
        /** @var ExpressGateway */
        $gateway = $this->getGateway();
        $invest = $this->getInvest();

        $transactionId = sprintf("0000000000-%s", $invest->id);
        if ($invest->project) {
            $project = Project::get($invest->project);
            $transactionId = sprintf("%s-%s", $project->getNumericId(), $invest->id);
        }

        $invest->setPreapproval($transactionId);

        // You can specify your paypal gateway details in config/settings.yml
        if (!$gateway->getLogoImageUrl()) $gateway->setLogoImageUrl(SRC_URL . '/goteo_logo.png');

        $gateway->setCurrency(Currency::getDefault('id'));

        $request = $gateway->purchase([
            'amount' => (float) $this->getTotalAmount(),
            'currency' => $gateway->getCurrency(),
            'description' => $this->getInvestDescription(),
            'returnUrl' => $this->getCompleteUrl(),
            'cancelUrl' => $this->getCompleteUrl(),
            'transactionId' => $transactionId,
        ]);

        return $request->send();
    }

    public function completePurchase(): ResponseInterface
    {
        /** @var ExpressGateway */
        $gateway = $this->getGateway();
        $invest = $this->getInvest();

        $gateway->setCurrency(Currency::getDefault('id'));

        $payment = $gateway->completePurchase([
            'amount' => (float) $this->getTotalAmount(),
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
