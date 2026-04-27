<?php

namespace Goteo\Payment\Method;

use Goteo\Application\Config;
use Goteo\Application\Currency;
use Goteo\Model\Project;
use Goteo\Payment\Method\AbstractPaymentMethod;
use Omnipay\Common\Message\ResponseInterface;

class RedsysPaymentMethod extends AbstractPaymentMethod
{
    public const PAYMENT_METHOD_ID = 'redsys';

    public static function getId(): string
    {
        return self::PAYMENT_METHOD_ID;
    }

    public function getIdNonStatic(): string
    {
        return self::PAYMENT_METHOD_ID;
    }

    public function getName(): string
    {
        return 'Redsys';
    }

    public function getDesc(): string
    {
        return 'Redsys gateway via Colonya';
    }

    public function getIcon(): string
    {
        return SRC_URL . '/assets/img/pay/card.png';
    }

    public function purchase(): ResponseInterface
    {
        /** @var \Omnipay\Redsys\Gateway */
        $gateway = $this->getGateway();
        $gateway->setMerchantKey(Config::get('payments.redsys.merchantKey'));
        $gateway->setMerchantCode(Config::get('payments.redsys.merchantCode'));
        $gateway->setTerminal(Config::get('payments.redsys.terminal'));
        $gateway->setCurrency(Currency::getDefault('id'));

        $invest = $this->getInvest();

        $transactionId = sprintf("000000%s", $invest->id);
        if ($invest->project) {
            $project = Project::get($invest->project);
            $transactionId = sprintf("%s%s", $project->getNumericId(6), $invest->id);
        }

        $invest->setPreapproval($transactionId);

        $gateway->setParameter('merchantData', json_encode([
            'project' => $project->id,
            'invest' => $invest->id
        ]));

        $request = $gateway->purchase()
            ->setAmount($this->getTotalAmount() * 100)
            ->setDescription($this->getInvestDescription())
            ->setReturnUrl($this->getCompleteUrl())
            ->setCancelUrl($this->getCompleteUrl())
            ->setTransactionId($transactionId);

        return $request->send();
    }

    public function completePurchase(): ResponseInterface
    {
        /** @var \Omnipay\Redsys\Gateway */
        $gateway = $this->getGateway();
        $gateway->setMerchantKey(Config::get('payments.redsys.merchantKey'));

        $response = $gateway->completePurchase();

        $invest = $this->getInvest();

        if ($response->getData()['success']) {
            $invest->setPayment($response->getData()['decodedParameters']['Ds_AuthorisationCode']);
        }

        return $response->send();
    }
}
