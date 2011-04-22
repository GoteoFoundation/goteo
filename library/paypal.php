<?php
namespace Goteo\Library {

    use Goteo\Model\Invest;
	/*
	 * Clase para usar los adaptive payments de paypal
	 */
    require_once 'library/paypal/adaptivepayments.php';  // SDK paypal para operaciones API

    class Paypal {

        /*
        public
            $client, // array de datos del usuario (Id,
            $account, // cuenta de paypal, sender
            $returnUrl, // retorno desde paypal a esta cuando confirman
            $cancelUrl, // si cancelan la transacción en paypal cuelve a esta
            $preapprovalKey, // si estamos ejecutando
            $errors; // los errores van aqui
        */

       /*
        *  Al constructor le decimos si es una operación con intervencion de usuario (aporte a proyecto)
        * o si es una operación de sistema (ejecución pre approval)
        *

        public function __construct($type, $data) {
            switch ($type) {
                case 'user':
                    // el dato es el id del proyecto para redireccionarlo
                    break;
                case 'system':
                    // el dato es el codigo de preapproval
                    break;
            }
        }

        *
        */


        /*
         * @param invest numeric id del registro de aporte
         * @param user string id del usaurio
         * @param project string id del proyecto
         *
         *
         * Método para crear un preapproval para un aporte
         * va a mandar al usuario a paypal para que confirme
         *
         * Necesita la cantidad del aporte
         * un solo pago
         *
         * desde hoy a 100 dias,
         * @TODO limite a los dias que le quede al proyecto segun los primeros 40 o los segundos 40 (hsta 80)
         */
        public static function preapproval($invest, $user, $project, $account, $amount) {
            
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
		           $returnURL = "http://devgoteo.org/invest/confirmed/" . $project; // a difundirlo @TODO mensaje gracias si llega desde un preapproval
		           $cancelURL = "http://devgoteo.org/invest/fail/" . $project . "/" . $invest; // a la página de aportar para intentarlo de nuevo

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
		           $preapprovalRequest->clientDetails->customerId = $user;
		           $preapprovalRequest->clientDetails->applicationId = APPLICATION_ID;
		           $preapprovalRequest->clientDetails->deviceId = DEVICE_ID;
		           $preapprovalRequest->clientDetails->ipAddress = "127.0.0.1";
		           $preapprovalRequest->currencyCode = "EUR";
		           $preapprovalRequest->startingDate = $startDate;
		           $preapprovalRequest->endingDate = $endDate;
		           $preapprovalRequest->maxNumberOfPayments = 1;
		           $preapprovalRequest->maxTotalAmountOfAllPayments = $amount;
		           $preapprovalRequest->requestEnvelope = new \RequestEnvelope();
		           $preapprovalRequest->requestEnvelope->errorLanguage = "es_ES";
		           $preapprovalRequest->senderEmail = 'julian_1302552287_per@gmail.com'; // @TODO, cuenta de paypal del usuario
                   $preapprovalRequest->feesPayer = "SENDER";

		           $ap = new \AdaptivePayments();
		           $response=$ap->Preapproval($preapprovalRequest);

		           if(strtoupper($ap->isSuccess) == 'FAILURE') {
                       die('ERROR: ' . $ap->getLastError()); //@FIXME obviusly
					} else {

                        // Guardar el codigo de preaproval en el registro de aporte y mandarlo a paypal
						$token = $response->preapprovalKey;
                        if (!empty($token)) {
                            Invest::setPreapproval($invest, $token);
                            $payPalURL = PAYPAL_REDIRECT_URL.'_ap-preapproval&preapprovalkey='.$token;
                            header("Location: ".$payPalURL);
                        } else {
                            die('No preapproval key obtained. <pre>' . print_r($response, 1) . '</pre>');
                        }
					}
			}
			catch(Exception $ex) {

				$fault = new \FaultMessage();
				$errorData = new \ErrorData();
				$errorData->errorId = $ex->getFile() ;
  				$errorData->message = $ex->getMessage();
		  		$fault->error = $errorData;

                die('ERROR: <pre>' . print_r($fault, 1) . '</pre>'); //@FIXME obviusly
			}
            
        }


        public static function pay($account, $amount) {

            return false;

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