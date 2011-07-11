<?php
namespace Goteo\Library {

    use Goteo\Model\Invest,
        Goteo\Core\Redirection;

    require_once 'library/tpv/wshandler.php';  // Libreria para comunicaciones con el webservice TPV y log

	/*
	 * Clase para usar el tpv de Caja Laboral mediante Sermepa
	 */
    class Tpv {

        /*
         * @param invest instancia del aporte: id, usuario, proyecto, cuenta, cantidad
         *
         * Método para crear un preapproval para un aporte (version tpv)
         * va a mandar al usuario al tpv para que confirme
         *
         * para sermepa el máximo de pre-apprioval son 45 días
         */
        public static function preapproval($invest, &$errors = array()) {

            /*
            Castellano-001,
            Inglés-002,
            Catalán-003,
            Francés-004,
            Alemán-005
            Holandés-006,
            Italiano-007
            Portugués-009,
            Valenciano-010
            Gallego-012
            Euskera-013
            */
            
			try {
                // preparo codigo y cantidad
                $token  = $invest->id . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9);
                $amount = $invest->amount * 100;

                // Guardar el codigo de preaproval en el registro de aporte (para confirmar o cancelar)
                $invest->setPreapproval($token);

                $MerchantID = TPV_MERCHANT_CODE;
                $currency = '978';
//                $transactionType = 0; // cero para un pago normal
                $transactionType = 7; //siete para iniciar un pre-autenticacion
//                $urlMerchant = SITE_URL."/tpv/comunication";
                $urlMerchant = "http://facturaweb.onliners-web.com/goteo/tpv.php"; // hasta pasarlo a produccion
                $clave = TPV_ENCRYPT_KEY;

                // y la firma
                $Firma = sha1($amount.$token.$MerchantID.$currency.$transactionType.$urlMerchant.$clave);

                // comenzamos una transacción de tipo 'pre-autenticación', 40 dias para confirmar la operacion
                // pero primero transaction 0 para desarrollo

                $datos = array(
                    'Ds_Merchant_MerchantCode'		=> $MerchantID,
                    'Ds_Merchant_Terminal'			=> '1',
                    'Ds_Merchant_TransactionType'	=> $transactionType,
                    'Ds_Merchant_MerchantSignature'	=> $Firma,
                    'Ds_Merchant_MerchantUrl'		=> $urlMerchant,
                    'Ds_Merchant_UrlOK'             => SITE_URL."/invest/confirmed/" . $invest->project,
                    'Ds_Merchant_UrlKO'             => SITE_URL."/invest/fail/" . $invest->project . "/" . $invest->id,
                    'Ds_Merchant_Currency'			=> $currency,
                    'Ds_Merchant_Order' 			=> $token,
                    'Ds_Merchant_ProductDescription'=> "Aporte de {$invest->amount} euros al proyecto {$invest->project}",
                    'Ds_Merchant_Amount'			=> $amount,
                    'Ds_Merchant_ConsumerLanguage'  => '001',
                    'Ds_Merchant_MerchantData'     => 'InvestId='.$invest->id.'&User='.$_SESSION['user']->id
                );

                // mandarlo al tpv
                $urlTPV = TPV_REDIRECT_URL;
                $data = '';
                $MsgStr = '';
                foreach ($datos as $n => $v) {
                    $data .= '<input name="'.$n.'" type="hidden" value="'.$v.'" />';
                    $MsgStr .= "{$n}:'{$v}'; ";
                }

                $conf = array('mode' => 0600, 'timeFormat' => '%X %x');
                $logger = &\Log::singleton('file', 'logs/'.date('Ymd').'_invest.log', 'caller', $conf);

                $logger->log('##### TPV '.date('d/m/Y').' User:'.$_SESSION['user']->id.'#####');

                $logger->log("request: $MsgStr");
                $logger->close();

                echo '<html><head><title>Goteo.org</title></head><body><form action="'.$urlTPV.'" method="post" id="form_tpv">'.$data.'</form><script type="text/javascript">document.getElementById("form_tpv").submit();</script></body></html>';

                die;
			}
			catch(Exception $ex) {

                $errors[] = 'Error fatal en la comunicación con el TPV, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-tpv-fault@doukeshi.org', 'Error fatal en comunicacion TPV Sermepa', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
			}
            
        }


        /*
         *  Metodo para ejecutar pago en el banco)
         * Recibe parametro del aporte (id, cuenta, cantidad)
         */
        public static function pay($invest, &$errors = array()) {

            try {
                // el codigo de pedido id+4xrand
                $token = $invest->preapproval;

                $amount = $invest->amount * 100;

                $MerchantID = TPV_MERCHANT_CODE;
                $currency = '978';
                $transactionType = 8; //ocho para confirmar una pre-autenticacion
                //no es necesario la url del comercio, no hay comunicación online
//                $urlMerchant = SITE_URL."/tpv/comunication";
//                $urlMerchant = "http://facturaweb.onliners-web.com/goteo/tpv.php"; // hasta pasarlo a produccion
                $clave = TPV_ENCRYPT_KEY;

                // y la firma
                $Firma = sha1($amount.$token.$MerchantID.$currency.$transactionType.$clave);

                // con el codigo Preapproval de id+4xrand confirmamos al autenticacion
                $xml  = '<DATOSENTRADA>';
                $xml .= '<DS_Version>1.0</DS_Version>';
                $xml .= '<DS_MERCHANT_AMOUNT>'.$amount.'</DS_MERCHANT_AMOUNT>';
                $xml .= '<DS_MERCHANT_CURRENCY>'.$currency.'</DS_MERCHANT_CURRENCY>';
                $xml .= '<DS_MERCHANT_TRANSACTIONTYPE>'.$transactionType.'</DS_MERCHANT_TRANSACTIONTYPE>';
                $xml .= '<DS_MERCHANT_MERCHANTSIGNATURE>'.$Firma.'</DS_MERCHANT_MERCHANTSIGNATURE>';
                $xml .= '<DS_MERCHANT_TERMINAL>1</DS_MERCHANT_TERMINAL>';
                $xml .= '<DS_MERCHANT_MERCHANTCODE>'.TPV_MERCHANT_CODE.'</DS_MERCHANT_MERCHANTCODE>';
                $xml .= '<DS_MERCHANT_ORDER>'.$token.'</DS_MERCHANT_ORDER>';
                $xml .= '</DATOSENTRADA>';

                // curl para comunicarnos con el webservice
                // usamos el mismo log de paypal para guardar el xml enviado y el xml de respuesta
                $handler = new \WSHandler();
                $response = $handler->callWebService($xml);

               if(strtoupper($handler->isSuccess) == 'FAILURE') {
                   $errors[] = 'No se ha podido iniciar la comunicación con paypal para procesar la preaprovación del cargo. ' . $ap->getLastError();
                   return false;
                }

                // parsea el xml de respuesta
                $data = \simplexml_load_string($response);
                // si devuelve un codigo de error
                if (\substr($data->CODIGO, 0, 3) == 'SIS') {
                    return false;
                } else {
                    // marcar el aporte como cargado
                    $invest->setPayment($data->OPERACION->Ds_AuthorisationCode);
                    return true;
                }
                    
            }
            catch (Exception $e) {
                $errors[] = 'Error fatal en la comunicación con tpv, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-tpv-fault@doukeshi.org', 'Error fatal en comunicacion tpv', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }

        }


        /*
         * El tpv no tiene detalles de preapproval,
         * solamente los que recibimos en la notificacion online al iniciar la transaccion
         *
        public static function preapprovalDetails ($key, &$errors = array()) {
            try {
                // en todo caso un resumen escrito del estado del aporte
                return 'tpvCODE';
            }
            catch(Exception $ex) {
                $errors[] = 'Error fatal en la comunicación con tpv, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-tpv-fault@doukeshi.org', 'Error fatal en comunicacion tpv', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }
        }
         *
         */

        /*
         * Llamada a tpv para obtener los detalles de un cargo
         * solamente los que recibimos como respuesta del webservice al confirmar
         *
        public static function paymentDetails ($key, &$errors = array()) {
            try {
                // en todo caso podemos mostrar los xml de petición y respuesta bien parseados
                return 'tpcTRANSACTIONid';
            }
            catch(Exception $ex) {
                $errors[] = 'Error fatal en la comunicación con tpv, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-tpv-fault@doukeshi.org', 'Error fatal en comunicacion tpv', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }
        }
         *
         */


        /*
         * Llamada para cancelar un preapproval (si llega a los 40 sin conseguir el mínimo)
         * tambien usando el webservice y con el preaproval code id+4xrand que le generamos en su dia
         */
        public static function cancelPreapproval ($invest, &$errors = array()) {
            try {
                // la transaccion se cancela sola, no hay movimiento contable y no genera costes
                // cancelar el aporte
                $invest->cancel();
                return true;
            }
            catch(Exception $ex) {
                $errors[] = 'Error fatal en la comunicación con tpv, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-tpv-fault@doukeshi.org', 'Error fatal en comunicacion tpv', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }
        }

	}
	
}