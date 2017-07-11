<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteodev\Payment;

use Symfony\Component\HttpFoundation\Response;
use Omnipay\Common\Message\ResponseInterface;

use Goteo\Payment\Method\AbstractPaymentMethod;
use Goteo\Library\Currency;
use Goteo\Util\Omnipay\Message\EmptyFailedResponse;
use Goteo\Util\Omnipay\Message\EmptySuccessfulResponse;
use Goteo\Application\App;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestEvent;

/**
 * This class is just an example and must NOT be used in production
 * Creates a Payment Method that does nothing!
 * Does not use Omnipay
 */
class DummyPaymentMethod extends AbstractPaymentMethod {
    private $simulating_gateway = false;

    public function getGatewayName() {
        return 'Dummy';
    }

    public function getName() {
        return 'Dummy Payment';
    }

    public function getDesc() {
        return 'Not really a payment, just for testing';
    }

    public function getIcon() {
        return SRC_URL . '/assets/img/pay/cash.png';
    }

    public function getDefaultHttpResponse(ResponseInterface $response) {
        if(!$this->simulating_gateway) return null;

        // Let's obtain the gateway and the
        $output = '<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Dummy Payment Gateway</title>
        <style>
        body {background:#f0f0f0;margin:20px}
        .center {margin: auto; width: 500px; border:5px solid #B5DADC; padding: 20px; background:#fff;}
        form input {padding:15px;font-size:1.5em}
        form.ok {float:left}
        form.ok submit{float:left}
        form.ok input {color:#090}
        form.ko {float:right}
        form.ko input {color:#b00}
        .clear {clear:both}
        </style>
    </head>
    <body>
        <div class="center">
        <h1>Dummy payment gateway</h1>
        <h3>This is a test gateway for testing. It does... nothing!</h3>
        <h4>%3$s</h4>
            <p>Choose what kind of payment do you wish:</p>
            <form class="ok" action="%1$s" method="post">
                <input type="hidden" name="number" value="4242424242424242">
                <input type="submit" value="Successful Payment" />
            </form>
            <form class="ko" action="%2$s" method="post">
                <input type="hidden" name="number" value="4111111111111111">
                <input type="submit" value="Failed Payment" />
            </form>
            <div class="clear"></div>
        </div>
    </body>
</html>';
        return new Response(sprintf(
                    $output,
                    htmlentities($this->getCompleteUrl(), ENT_QUOTES, 'UTF-8', false),
                    htmlentities($this->getCompleteUrl(), ENT_QUOTES, 'UTF-8', false),
                    $this->getInvest()->amount . ' ' .Currency::getDefault('html')
                ));
    }

    public function purchase() {
        $this->simulating_gateway = true;
        return new EmptyFailedResponse();
    }

    public function completePurchase() {

        // Let's obtain the gateway and the
        $gateway = $this->getGateway();
        $gateway->setCurrency(Currency::getDefault('id'));
        $request = $this->getRequest();
        $invest = $this->getInvest();
        $payment = $gateway->purchase([
                    'amount' => (float) $this->getInvest()->amount,
                    'card' => [
                        'number' => $request->request->get('number'),
                        'expiryMonth' => '12',
                        'expiryYear' => '2017',
                        ],
                    'description' => $this->getInvestDescription(),
                    'returnUrl' => $this->getCompleteUrl(),
                    'cancelUrl' => $this->getCompleteUrl(),
        ]);
        // set the dummy card as payment detail data
        $invest->setPayment($request->request->get('number'));

        return $payment->send();
    }

    public function refundable() {
        return true;
    }

    public function refund() {
        // Any plugin can throw a PaymentException here in order to abort the refund process
        App::dispatch(AppEvents::INVEST_REFUND, new FilterInvestEvent($this->getInvest(), $this));

        return new EmptySuccessfulResponse();
    }
}
