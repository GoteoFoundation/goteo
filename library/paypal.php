<?php
namespace Goteo\Library {

	/*
	 * Clase para usar los adaptive payments de paypal
	 */
    // Include the service wrapper class and the constants file
    require_once 'library/paypal/AdaptivePayments.php';  // SDK paypal para operaciones API
    require_once 'library/paypal/Config/web_constants.php'; // Parametros de la web que se usan en la comunicación con paypal

    class Paypal {
		
        public static function pay($account, $amount) {

            $msg = '';

            // Create request object
            $payRequest = new \PayRequest();
            $payRequest->actionType = "PAY";
            $returnURL = 'http://devgoteo.org';
            $cancelURL = 'http://devgoteo.org';
            $payRequest->cancelUrl = $cancelURL ;
            $payRequest->returnUrl = $returnURL;
            $payRequest->clientDetails = new \ClientDetailsType();
            $payRequest->clientDetails->applicationId ='APP-80W284485P519543T';
            $payRequest->clientDetails->deviceId = '127001';
            $payRequest->clientDetails->ipAddress = '127.0.0.1';
            $payRequest->currencyCode = 'EUR';
            $payRequest->senderEmail = $_POST['email'];
            $payRequest->requestEnvelope = new \RequestEnvelope();
            $payRequest->requestEnvelope->errorLanguage = 'es_ES';

            $receiver1 = new \receiver();
            $receiver1->email = 'goteo_1302553021_biz@gmail.com';
            $receiver1->amount = $_POST['amount'];

            $payRequest->receiverList = array($receiver1);


            // Create service wrapper object
            $ap = new \AdaptivePayments();

            // invoke business method on service wrapper passing in appropriate request params
            $response = $ap->Pay($payRequest);

            // Check response
            if(strtoupper($ap->isSuccess) == 'FAILURE')
            {
                $soapFault = $ap->getLastError();
                $msg .= "Transaction Pay Failed: error Id: ";
                if(is_array($soapFault->error)) {
                    $msg .= $soapFault->error[0]->errorId . ", error message: " . $soapFault->error[0]->message ;
                } else {
                    $msg .= $soapFault->error->errorId . ", error message: " . $soapFault->error->message ;
                }
            } else {
                $token = $response->payKey;
                $msg .= "Transaction Successful! PayKey is $token \n";
            }

            return $msg;

        }
	}
	
}