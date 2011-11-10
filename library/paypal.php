<?php
namespace Goteo\Library {

    use Goteo\Model\Invest,
        Goteo\Model\Project,
        Goteo\Model\User,
        Goteo\Library\Feed,
        Goteo\Core\Redirection;

    require_once 'library/paypal/adaptivepayments.php';  // SDK paypal para operaciones API (minimizado)

	/*
	 * Clase para usar los adaptive payments de paypal
	 */
    class Paypal {

        /**
         * @param object invest instancia del aporte: id, usuario, proyecto, cuenta, cantidad
         *
         * Método para crear un preapproval para un aporte
         * va a mandar al usuario a paypal para que confirme
         *
         * @TODO poner límite máximo de dias a lo que falte para los 40/80 dias para evitar las cancelaciones
         */
        public static function preapproval($invest, &$errors = array()) {
            
			try {
                $project = Project::getMini($invest->project);

		           /* The returnURL is the location where buyers return when a
		            payment has been succesfully authorized.
		            The cancelURL is the location buyers are sent to when they hit the
		            cancel button during authorization of payment during the PayPal flow                 */
                
                    $returnURL = SITE_URL."/invest/confirmed/" . $invest->project . "/" . $invest->id; // a difundirlo @TODO mensaje gracias si llega desde un preapproval
                    $cancelURL = SITE_URL."/invest/fail/" . $invest->project . "/" . $invest->id; // a la página de aportar para intentarlo de nuevo

                    // desde hoy hasta los dias que le falten para finalizar la ronda (mas uno porque no queremos pillarnos los dedos por ser el mismo día)
                    $remain = Project::daysRemain($invest->project);
                    $remain++;

                    date_default_timezone_set('UTC');
                    $currDate = getdate();
                    $hoy = $currDate['year'].'-'.$currDate['mon'].'-'.$currDate['mday'];
                    $startDate = strtotime($hoy);
                    $startDate = date('Y-m-d', mktime(date('h',$startDate),date('i',$startDate),0,date('m',$startDate),date('d',$startDate),date('Y',$startDate)));
                    $endDate = strtotime($hoy);
                    $endDate = date('Y-m-d', mktime(0,0,0,date('m',$endDate),date('d',$endDate)+$remain,date('Y',$endDate)));


		           /* Make the call to PayPal to get the preapproval token
		            If the API call succeded, then redirect the buyer to PayPal
		            to begin to authorize payment.  If an error occured, show the
		            resulting errors
		            */
		           $preapprovalRequest = new \PreapprovalRequest();
                   $preapprovalRequest->memo = "Aporte de {$invest->amount} EUR al proyecto: {$project->name}";
		           $preapprovalRequest->cancelUrl = $cancelURL;
		           $preapprovalRequest->returnUrl = $returnURL;
		           $preapprovalRequest->clientDetails = new \ClientDetailsType();
		           $preapprovalRequest->clientDetails->customerId = $invest->user->id;
		           $preapprovalRequest->clientDetails->applicationId = PAYPAL_APPLICATION_ID;
		           $preapprovalRequest->clientDetails->deviceId = PAYPAL_DEVICE_ID;
		           $preapprovalRequest->clientDetails->ipAddress = $_SERVER['REMOTE_ADDR'];
		           $preapprovalRequest->currencyCode = "EUR";
		           $preapprovalRequest->startingDate = $startDate;
		           $preapprovalRequest->endingDate = $endDate;
		           $preapprovalRequest->maxNumberOfPayments = 1;
		           $preapprovalRequest->displayMaxTotalAmount = true;
		           $preapprovalRequest->feesPayer = 'EACHRECEIVER';
		           $preapprovalRequest->maxTotalAmountOfAllPayments = $invest->amount;
		           $preapprovalRequest->requestEnvelope = new \RequestEnvelope();
		           $preapprovalRequest->requestEnvelope->errorLanguage = "es_ES";

		           $ap = new \AdaptivePayments();
		           $response=$ap->Preapproval($preapprovalRequest);

		           if(strtoupper($ap->isSuccess) == 'FAILURE') {
                       $errors[] = 'No se ha podido iniciar la comunicación con paypal para procesar la preaprovación del cargo. ' . $ap->getLastError();
                        @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . ' ap->success = FAILURE.<br /><pre>' . print_r($ap, 1) . '</pre><pre>' . print_r($response, 1) . '</pre>');
                        return false;
					}

                    // Guardar el codigo de preaproval en el registro de aporte y mandarlo a paypal
                    $token = $response->preapprovalKey;
                    if (!empty($token)) {
                        $invest->setPreapproval($token);
                        $payPalURL = PAYPAL_REDIRECT_URL.'_ap-preapproval&preapprovalkey='.$token;
                        throw new \Goteo\Core\Redirection($payPalURL, Redirection::TEMPORARY);
                        return true;
                    } else {
                        $errors[] = 'No preapproval key obtained. <pre>' . print_r($response, 1) . '</pre>';
                        @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . ' No preapproval key obtained.<br /><pre>' . print_r($response, 1) . '</pre>');
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
                @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
			}
            
        }


        /*
         *  Metodo para ejecutar pago (desde cron)
         * Recibe parametro del aporte (id, cuenta, cantidad)
         *
         * Es un pago encadenado, la comision del 8% a Goteo y el resto al proyecto
         *
         */
        public static function pay($invest, &$errors = array()) {

            try {
                $project = Project::getMini($invest->project);
                $userData = User::getMini($invest->user);

                // al productor le pasamos el importe del cargo menos el 8% que se queda goteo
                $amountPay = $invest->amount - ($invest->amount * 0.08);


                // Create request object
                $payRequest = new \PayRequest();
                $payRequest->actionType = "PAY";
                $payRequest->memo = "Cargo del aporte de {$invest->amount} EUR del usuario '{$invest->user->name}' al proyecto '{$project->name}'";
                $payRequest->cancelUrl = SITE_URL.'/invest/charge/fail/' . $invest->id;
                $payRequest->returnUrl = SITE_URL.'/invest/charge/success/' . $invest->id;
                $payRequest->clientDetails = new \ClientDetailsType();
		        $payRequest->clientDetails->customerId = $invest->user->id;
                $payRequest->clientDetails->applicationId = PAYPAL_APPLICATION_ID;
                $payRequest->clientDetails->deviceId = PAYPAL_DEVICE_ID;
                $payRequest->clientDetails->ipAddress = PAYPAL_IP_ADDRESS;
                $payRequest->currencyCode = 'EUR';
           		$payRequest->preapprovalKey = $invest->preapproval;
                $payRequest->actionType = 'PAY_PRIMARY';
                $payRequest->feesPayer = 'EACHRECEIVER';
                // SENDER no vale para chained payments   (PRIMARYRECEIVER, EACHRECEIVER, SECONDARYONLY)
                $payRequest->requestEnvelope = new \RequestEnvelope();
                $payRequest->requestEnvelope->errorLanguage = 'es_ES';

                // Primary receiver, Goteo Business Account
                $receiverP = new \receiver();
                $receiverP->email = PAYPAL_BUSINESS_ACCOUNT; // tocar en config para poner en real
                $receiverP->amount = (int) $invest->amount;
                $receiverP->primary = true;

                // Receiver, Projects PayPal Account
                $receiver = new \receiver();
                $receiver->email = $invest->account;
                $receiver->amount = $amountPay;
                $receiver->primary = false;

                $payRequest->receiverList = array($receiverP, $receiver);

                // Create service wrapper object
                $ap = new \AdaptivePayments();

                // invoke business method on service wrapper passing in appropriate request params
                $response = $ap->Pay($payRequest);

                // Check response
                if(strtoupper($ap->isSuccess) == 'FAILURE') {
                    $soapFault = $ap->getLastError();
                    if(is_array($soapFault->error)) {
                        $errorId = $soapFault->error[0]->errorId;
                        $errorMsg = $soapFault->error[0]->message;
                    } else {
                        $errorId = $soapFault->error->errorId;
                        $errorMsg = $soapFault->error->message;
                    }
                    if (is_array($soapFault->payErrorList->payError)) {
                        $errorId = $soapFault->payErrorList->payError[0]->error->errorId;
                        $errorMsg = $soapFault->payErrorList->payError[0]->error->message;
                    }

                    // tratamiento de errores
                    switch ($errorId) {
                        case '569013': // preapproval cancelado por el usuario desde panel paypal
                        case '539012': // preapproval no se llegó a autorizar
                            if ($invest->cancel()) {
                                $action = 'Aporte cancelado';

                                /*
                                 * Evento Feed
                                 */
                                $log = new Feed();
                                $log->title = 'Aporte cancelado por preaproval cancelado por el usuario paypal';
                                $log->url = '/admin/invests';
                                $log->type = 'system';
                                $log_text = 'Se ha <span class="red">Cancelado</span> el aporte de %s de %s (id: %s) al proyecto %s del dia %s por preapproval cancelado';
                                $items = array(
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('money', $invest->amount.' &euro;'),
                                    Feed::item('system', $invest->id),
                                    Feed::item('project', $project->name, $project->id),
                                    Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                                );
                                $log->html = \vsprintf($log_text, $items);
                                $log->add($errors);
                                unset($log);

                            }
                            break;
                    }


                    if (empty($errorId)) {
                        $errors[] = 'NO es soapFault pero no es Success: <pre>' . print_r($ap, 1) . '</pre>';
                        @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . ' No es un soap fault pero no es un success.<br /><pre>' . print_r($ap, 1) . '</pre>');
                    } else {
                        $errors[] = "$action $errorMsg [$errorId]";
                    }
                    
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
                    @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . ' No payment key obtained.<br /><pre>' . print_r($response, 1) . '</pre>');
                    return false;
                }
    
            }
            catch (Exception $e) {
                $fault = new \FaultMessage();
                $errorData = new \ErrorData();
                $errorData->errorId = $ex->getFile() ;
                $errorData->message = $ex->getMessage();
                $fault->error = $errorData;

                $errors[] = 'No se ha podido inicializar la comunicación con Paypal, se ha reportado la incidencia.';
                @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }

        }


        /*
         *  Metodo para ejecutar pago secundario (desde cron/dopay)
         * Recibe parametro del aporte (id, cuenta, cantidad)
         */
        public static function doPay($invest, &$errors = array()) {

            try {
                $project = Project::getMini($invest->project);
                $userData = User::getMini($invest->user);

                // Create request object
                $payRequest = new \ExecutePaymentRequest();
           		$payRequest->payKey = $invest->payment;
           		$payRequest->requestEnvelope = 'SOAP';

                // Create service wrapper object
                $ap = new \AdaptivePayments();

                // invoke business method on service wrapper passing in appropriate request params
                $response = $ap->ExecutePayment($payRequest);

                // Check response
                if(strtoupper($ap->isSuccess) == 'FAILURE') {
                    $soapFault = $ap->getLastError();
                    if(is_array($soapFault->error)) {
                        $errorId = $soapFault->error[0]->errorId;
                        $errorMsg = $soapFault->error[0]->message;
                    } else {
                        $errorId = $soapFault->error->errorId;
                        $errorMsg = $soapFault->error->message;
                    }
                    if (is_array($soapFault->payErrorList->payError)) {
                        $errorId = $soapFault->payErrorList->payError[0]->error->errorId;
                        $errorMsg = $soapFault->payErrorList->payError[0]->error->message;
                    }

                    // tratamiento de errores
                    switch ($errorId) {
                        case '569013': // preapproval cancelado por el usuario desde panel paypal
                        case '539012': // preapproval no se llegó a autorizar
                            if ($invest->cancel()) {
                                $action = 'Aporte cancelado';

                                /*
                                 * Evento Feed
                                 */
                                $log = new Feed();
                                $log->title = 'Aporte cancelado por preaproval cancelado por el usuario paypal';
                                $log->url = '/admin/invests';
                                $log->type = 'system';
                                $log_text = 'Se ha <span class="red">Cancelado</span> el aporte de %s de %s (id: %s) al proyecto %s del dia %s por preapproval cancelado';
                                $items = array(
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('money', $invest->amount.' &euro;'),
                                    Feed::item('system', $invest->id),
                                    Feed::item('project', $project->name, $project->id),
                                    Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                                );
                                $log->html = \vsprintf($log_text, $items);
                                $log->add($errors);
                                unset($log);

                            }
                            break;
                    }


                    if (empty($errorId)) {
                        $errors[] = 'NO es soapFault pero no es Success: <pre>' . print_r($ap, 1) . '</pre>';
                        @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . ' No es un soap fault pero no es un success.<br /><pre>' . print_r($ap, 1) . '</pre>');
                    } else {
                        $errors[] = "$action $errorMsg [$errorId]";
                    }

                    return false;
                }

                // verificar el campo paymentExecStatus
                if (!empty($response->paymentExecStatus) && $response->paymentExecStatus == 'COMPLETED') {
                    if ($invest->setStatus('3')) {
                        return true;
                    } else {
                        $errors[] = "Obtenido estatus de ejecución {$response->paymentExecStatus} pero no se ha actualizado el registro de aporte id {$invest->id}.";
                        return false;
                    }
                } else {
                    $errors[] = 'No se ha completado el pago. <pre>' . print_r($response, 1) . '</pre>';
                    @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . ' No payment exec status completed.<br /><pre>' . print_r($response, 1) . '</pre>');
                    return false;
                }

            }
            catch (Exception $e) {
                $fault = new \FaultMessage();
                $errorData = new \ErrorData();
                $errorData->errorId = $ex->getFile() ;
                $errorData->message = $ex->getMessage();
                $fault->error = $errorData;

                $errors[] = 'No se ha podido inicializar la comunicación con Paypal, se ha reportado la incidencia.';
                @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
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
                @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
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
                @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }
        }


        /*
         * Llamada para cancelar un preapproval (si llega a los 40 sin conseguir el mínimo)
         * recibe la instancia del aporte
         */
        public static function cancelPreapproval ($invest, &$errors = array()) {
            try {
                if (empty($invest->preapproval)) {
                    $invest->cancel();
                    return true;
                }

                $CPRequest = new \CancelPreapprovalRequest();

                $CPRequest->requestEnvelope = new \RequestEnvelope();
                $CPRequest->requestEnvelope->errorLanguage = "es_ES";
                $CPRequest->preapprovalKey = $invest->preapproval;

                $ap = new \AdaptivePayments();
                $response = $ap->CancelPreapproval($CPRequest);


                if(strtoupper($ap->isSuccess) == 'FAILURE') {
                    $errors[] = 'Preapproval cancel failed.' . $ap->getLastError();
                    @mail('goteo-paypal-API-fault@doukeshi.org', 'Fallo al cancelar preapproval Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($ap->getLastError(), 1) . '</pre>');
                    return false;
                } else {
                    $invest->cancel();
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
                @mail('goteo-paypal-API-fault@doukeshi.org', 'Error fatal en comunicacion Paypal API', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }
        }

	}
	
}