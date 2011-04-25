<?php
namespace Goteo\Library {

    use Goteo\Model\Invest,
        Goteo\Core\Redirection;

    require_once 'library/paypal/adaptivepayments.php';  // SDK paypal para operaciones API (minimizado)

	/*
	 * Clase para usar los adaptive payments de paypal
	 */
    class Paypal {

        /*
         * @param invest instancia del aporte: id, usuario, proyecto, cuenta, cantidad
         *
         * Método para crear un preapproval para un aporte
         * va a mandar al usuario a paypal para que confirme
         *
         * @TODO limite a los dias que le quede al proyecto segun los primeros 40 o los segundos 40 (hsta 80)
         */
        public static function preapproval($invest, &$errors = array()) {
            
			try {

		        /* The servername and serverport tells PayPal where the buyer
		           should be directed back to after authorizing payment.
		           In this case, its the local webserver that is running this script
		           Using the servername and serverport, the return URL is the first
		           portion of the URL that buyers will return to after authorizing payment                */

		           /* The returnURL is the location where buyers return when a
		            payment has been succesfully authorized.
		            The cancelURL is the location buyers are sent to when they hit the
		            cancel button during authorization of payment during the PayPal flow                 */
		           $returnURL = PAYPAL_SITE_URL."/invest/confirmed/" . $invest->project; // a difundirlo @TODO mensaje gracias si llega desde un preapproval
		           $cancelURL = PAYPAL_SITE_URL."/invest/fail/" . $invest->project . "/" . $invest->id; // a la página de aportar para intentarlo de nuevo

                    // desde hoy hasta 40 dias
                    $currDate = getdate();
                    $hoy = $currDate['year'].'-'.$currDate['mon'].'-'.$currDate['mday'];
                    $startDate = strtotime($hoy);
                    $startDate = date('Y-m-d', mktime(0,0,0,date('m',$startDate),date('d',$startDate),date('Y',$startDate)));
                    $endDate = strtotime($hoy);
                    $endDate = date('Y-m-d', mktime(0,0,0,date('m',$endDate),date('d',$endDate)+40,date('Y',$endDate)));



		           /* Make the call to PayPal to get the preapproval token
		            If the API call succeded, then redirect the buyer to PayPal
		            to begin to authorize payment.  If an error occured, show the
		            resulting errors
		            */
		           $preapprovalRequest = new \PreapprovalRequest();
		           $preapprovalRequest->cancelUrl = $cancelURL;
		           $preapprovalRequest->returnUrl = $returnURL;
		           $preapprovalRequest->clientDetails = new \ClientDetailsType();
		           $preapprovalRequest->clientDetails->customerId = $invest->user;
		           $preapprovalRequest->clientDetails->applicationId = PAYPAL_APPLICATION_ID;
		           $preapprovalRequest->clientDetails->deviceId = PAYPAL_DEVICE_ID;
		           $preapprovalRequest->clientDetails->ipAddress = PAYPAL_IP_ADDRESS;
		           $preapprovalRequest->currencyCode = "EUR";
		           $preapprovalRequest->startingDate = $startDate;
		           $preapprovalRequest->endingDate = $endDate;
		           $preapprovalRequest->maxNumberOfPayments = 1;
		           $preapprovalRequest->maxTotalAmountOfAllPayments = $invest->amount;
		           $preapprovalRequest->requestEnvelope = new \RequestEnvelope();
		           $preapprovalRequest->requestEnvelope->errorLanguage = "es_ES";
		           $preapprovalRequest->senderEmail = $invest->account;
                   $preapprovalRequest->feesPayer = "SENDER";

		           $ap = new \AdaptivePayments();
		           $response=$ap->Preapproval($preapprovalRequest);

		           if(strtoupper($ap->isSuccess) == 'FAILURE') {
                       $errors[] = 'No se ha podido iniciar la comunicación con paypal para procesar la preaprovación del cargo. ' . $ap->getLastError();
                       return false;
					}

                    // Guardar el codigo de preaproval en el registro de aporte y mandarlo a paypal
                    $token = $response->preapprovalKey;
                    if (!empty($token)) {
                        $invest->setPreapproval($token);
                        $payPalURL = PAYPAL_REDIRECT_URL.'_ap-preapproval&preapprovalkey='.$token;
                        throw new \Goteo\Core\Redirection($payPalURL, Redirection::TEMPORARY);
                    } else {
                        $errors[] = 'No preapproval key obtained. <pre>' . print_r($response, 1) . '</pre>';
                        return false;
                    }

			}
			catch(Exception $ex) {

				$fault = new \FaultMessage();
				$errorData = new \ErrorData();
				$errorData->errorId = $ex->getFile() ;
  				$errorData->message = $ex->getMessage();
		  		$fault->error = $errorData;

                $errors[] = 'Error fatal en la comunicación con Paypal, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
			}
            
        }


        /*
         *  Metodo para ejecutar pago (desde cron)
         * Recibe parametro del aporte (id, cuenta, cantidad)
         */
        public static function pay($invest, &$errors = array()) {

            try {
                // Create request object
                $payRequest = new \PayRequest();
                $payRequest->actionType = "PAY";
                $payRequest->memo = "Ejecución del aporte de {$invest->amount} EUR al proyecto {$invest->project} en la plataforma Goteo";
                $payRequest->cancelUrl = PAYPAL_SITE_URL.'/cron/charge_fail/' . $invest->id;
                $payRequest->returnUrl = PAYPAL_SITE_URL.'/cron/charge_success/' . $invest->id;
                $payRequest->clientDetails = new \ClientDetailsType();
		        $payRequest->clientDetails->customerId = $invest->user;
                $payRequest->clientDetails->applicationId = PAYPAL_APPLICATION_ID;
                $payRequest->clientDetails->deviceId = PAYPAL_DEVICE_ID;
                $payRequest->clientDetails->ipAddress = PAYPAL_IP_ADDRESS;
                $payRequest->currencyCode = 'EUR';
                $payRequest->senderEmail = $invest->account;
                $payRequest->requestEnvelope = new \RequestEnvelope();
                $payRequest->requestEnvelope->errorLanguage = 'es_ES';

                $receiver = new \receiver();
                $receiver->email = PAYPAL_BUSINESS_ACCOUNT;
                $receiver->amount = $invest->amount;

                $payRequest->receiverList = array($receiver);

                // Create service wrapper object
                $ap = new \AdaptivePayments();

                // invoke business method on service wrapper passing in appropriate request params
                $response = $ap->Pay($payRequest);

                // Check response
                if(strtoupper($ap->isSuccess) == 'FAILURE') {
                    $soapFault = $ap->getLastError();
                    if(is_array($soapFault->error)) {
                        $msg = $soapFault->error[0]->errorId . ", error message: " . $soapFault->error[0]->message ;
                    } else {
                        $msg = $soapFault->error->errorId . ", error message: " . $soapFault->error->message ;
                    }
                    $errors[] = 'No se ha podido inicializar la comunicación con Paypal para la ejecución del cargo.';
                    $errors[] = $msg;
                    return false;
                }

                $token = $response->payKey;
                if (!empty($token)) {
                    if ($invest->setPayment($token)) {
                        return true;
                    } else {
                        $errors[] = "Obtenido codigo de pago $token pero no se ha grabado correctamente en el registro de aporte id {$invest->id}.";
                        return false;
                    }
                } else {
                    $errors[] = 'No payment key obtained. <pre>' . print_r($response, 1) . '</pre>';
                    return false;
                }
    
            }
            catch (Exception $e) {
                $fault = new \FaultMessage();
                $errorData = new \ErrorData();
                $errorData->errorId = $ex->getFile() ;
                $errorData->message = $ex->getMessage();
                $fault->error = $errorData;

                $errors[] = 'Error fatal en la comunicación con Paypal, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }

        }


        /*
         * Llamada a paypal para obtener los detalles de un preapproval
         */
        public static function preapprovalDetails ($key, &$errors = array()) {
            try {
                $PDRequest = new \PreapprovalDetailsRequest();

                $PDRequest->requestEnvelope = new \RequestEnvelope();
                $PDRequest->requestEnvelope->errorLanguage = "es_ES";
                $PDRequest->preapprovalKey = $key;

                $ap = new \AdaptivePayments();
                $response = $ap->PreapprovalDetails($PDRequest);

                if(strtoupper($ap->isSuccess) == 'FAILURE') {
                    $errors[] = 'No preapproval details obtained. <pre>' . print_r($ap->getLastError(), 1) . '</pre>';
                    return false;
                } else {
                    return $response;
                }
            }
            catch(Exception $ex) {

                $fault = new \FaultMessage();
                $errorData = new \ErrorData();
                $errorData->errorId = $ex->getFile() ;
                $errorData->message = $ex->getMessage();
                $fault->error = $errorData;

                $errors[] = 'Error fatal en la comunicación con Paypal, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }
        }

        /*
         * Llamada a paypal para obtener los detalles de un cargo
         */
        public static function paymentDetails ($key, &$errors = array()) {
            try {
                $pdRequest = new \PaymentDetailsRequest();
                $pdRequest->payKey = $key;
                $rEnvelope = new \RequestEnvelope();
                $rEnvelope->errorLanguage = "es_ES";
                $pdRequest->requestEnvelope = $rEnvelope;

                $ap = new \AdaptivePayments();
                $response=$ap->PaymentDetails($pdRequest);

                if(strtoupper($ap->isSuccess) == 'FAILURE') {
                    $errors[] = 'No payment details obtained. <pre>' . print_r($ap->getLastError(), 1) . '</pre>';
                    return false;
                } else {
                    return $response;
                }
            }
            catch(Exception $ex) {

                $fault = new FaultMessage();
                $errorData = new ErrorData();
                $errorData->errorId = $ex->getFile() ;
                $errorData->message = $ex->getMessage();
                $fault->error = $errorData;

                $errors[] = 'Error fatal en la comunicación con Paypal, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }
        }


        /*
         * Llamada para cancelar un preapproval (si llega a los 40 sin conseguir el mínimo)
         * recibe la instancia del aporte
         */
        public static function cancelPreapproval ($invest, &$errors = array()) {
            try {
                $CPRequest = new \CancelPreapprovalRequest();

                $CPRequest->requestEnvelope = new \RequestEnvelope();
                $CPRequest->requestEnvelope->errorLanguage = "es_ES";
                $CPRequest->preapprovalKey = $invest->code;

                $ap = new \AdaptivePayments();
                $response = $ap->CancelPreapproval($CPRequest);


                if(strtoupper($ap->isSuccess) == 'FAILURE') {
                    $errors[] = 'Preapproval cancel faild. <pre>' . print_r($ap->getLastError(), 1) . '</pre>';
                    return false;
                } else {
                    $invest->cancelPreapproval();
                    return true;
                }
            }
            catch(Exception $ex) {

                $fault = new \FaultMessage();
                $errorData = new \ErrorData();
                $errorData->errorId = $ex->getFile() ;
                $errorData->message = $ex->getMessage();
                $fault->error = $errorData;

                $errors[] = 'Error fatal en la comunicación con Paypal, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }
        }

	}
	
}