<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

// Paypal detail LIBRARY

namespace Goteo\Library;

use PayPal\Service\AdaptivePaymentsService;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\Types\AP\PaymentDetailsRequest;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use PayPal\Types\Common\RequestEnvelope;

use Goteo\Model\Invest;
use Goteo\Application\Config;

/*
 * Clase para usar los adaptive payments de paypal
 */

class Paypal
{

    private $invest;
    private $errors = [];

    public function __construct(Invest $invest) {
        $this->invest = $invest;
        if($this->invest->method !== 'paypal')  {
            $this->errors[] = 'This invest is not a Paypal Payment!';
        }
    }

    public function getErrors() {
        return $this->errors;
    }


    /*
     * Llamada a paypal para obtener los detalles de un cargo
     */
    public function paymentDetails()
    {
        $requestEnvelope = new RequestEnvelope("en_US");
        $paymentDetailsReq = new PaymentDetailsRequest($requestEnvelope);
        $serviceCall = 'PaymentDetails';
        // Old payments with preaproval
        if($this->invest->preapproval) {
            if($this->invest->payment) {
                $paymentDetailsReq->payKey = $this->invest->payment;
            } else {
                $paymentDetailsReq->preapprovalKey = $this->invest->preapproval;
                $serviceCall = 'PreapprovalDetails';
            }
         }
        elseif ($this->invest->transaction) {
            $paymentDetailsReq->shippingEnabled = false;
            if($this->invest->payment) {
                // New expresscheckout
                return $this->paymentExpressCheckoutDetails();
            }
            $paymentDetailsReq->transactionId = $this->invest->transaction;
        }

        try {
            $service = new AdaptivePaymentsService([
                "acct1.UserName" => Config::get('payments.paypal.username'),
                "acct1.Password" => Config::get('payments.paypal.password'),
                "acct1.Signature" => Config::get('payments.paypal.signature'),
                "acct1.AppId" => Config::get('payments.paypal.appId'),
                'mode' => Config::get('payments.paypal.testMode') ? 'sandbox' : 'live'
                ]);

            // llamada metodo handler

            $response = $service->$serviceCall($paymentDetailsReq);
            if (strtoupper($response->responseEnvelope->ack) == 'FAILURE') {
                // print_r($response);die;
                $this->errors[] = 'No payment details obtained. <pre>Errno: '.$response->error[0]->errorId. " Error: " . $response->error[0]->message  . '</pre>';
                return false;
            }

            return $response;

        } catch (\Exception $ex) {
            $this->errors[] = $ex->getMessage();
        }
        return false;
    }

    /*
     * Llamada a paypal para obtener los detalles de un cargo express checkout
     * NOT REALLY USEFULL, EXPRESS CHECKOUT HAS A TOKEN LIFE-TIME OF 3 HOURS
     */
    private function paymentExpressCheckoutDetails()
    {
        $paymentDetailsReq = new GetExpressCheckoutDetailsReq();
        $responseEnvelope = new GetExpressCheckoutDetailsRequestType($this->invest->payment);
        // $responseEnvelope->payKey = $this->invest->payment;
        $paymentDetailsReq->GetExpressCheckoutDetailsRequest = $responseEnvelope;

        try {
            $service = new PayPalAPIInterfaceServiceService([
                "acct1.UserName" => Config::get('payments.paypal.username'),
                "acct1.Password" => Config::get('payments.paypal.password'),
                "acct1.Signature" => Config::get('payments.paypal.signature'),
                "acct1.AppId" => Config::get('payments.paypal.appId'),
                'mode' => Config::get('payments.paypal.testMode') ? 'sandbox' : 'live'
                ]);

            // llamada metodo handler

            $response = $service->GetExpressCheckoutDetails($paymentDetailsReq);
            // $response = $response->GetExpressCheckoutDetailsResponseDetails;
            if (strtoupper($response->Ack) == 'FAILURE') {
                // print_r($response);die;
                $this->errors[] = 'No payment details obtained. <pre>Errno: '.$response->Errors[0]->ErrorCode. " Error: " . $response->Errors[0]->LongMessage  . '</pre>';
                return false;
            }

            return $response;

        } catch (\Exception $ex) {
            $this->errors[] = $ex->getMessage();
        }
        return false;
    }

}


