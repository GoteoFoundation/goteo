<?php
namespace Goteo\Library {

    use Goteo\Model\Invest,
        Goteo\Model\Project,
        Goteo\Core\Redirection;

    require_once 'library/tpv/wshandler.php';  // Libreria para comunicaciones con el webservice TPV y log

	/*
	 * Clase para usar el tpv de SaNostra mediante CECA
	 */
    class Tpv {

        static $langs = array(
            'es' => '1',
            'ca' => '2',
            'eu' => '3',
            'gl' => '4',
            'va' => '5',
            'en' => '6',
            'fr' => '7',
            'de' => '8',
            'pt' => '9',
            'it' => '10'
        );

        /*
         * para ceca no hay preapproval, es el cargo directamente
         */
        public static function preapproval($invest, &$errors = array()) {
            return static::pay($invest, $errors);
        }

        public static function pay($invest, &$errors = array()) {

			try {
                $project = Project::getMini($invest->project);

                // preparo codigo y cantidad
                $token  = $invest->id . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9);
                $amount = $invest->amount * 100;

                // Guardar el codigo de preaproval en el registro de aporte (para confirmar o cancelar)
                $invest->setPreapproval($token);

                $MerchantID = TPV_MERCHANT_CODE;
                $AcquirerBIN = '0000554041';
                $TerminalID = '00000003';
                $currency = '978';
                $exponent = '2';
                $cypher = 'SHA1';
                $urlMerchant = "http://www.goteo.org";
                $clave = TPV_ENCRYPT_KEY;

                $Url_OK = SITE_URL."/invest/confirmed/" . $invest->project . "/" . $invest->id;
                $Url_NOK = SITE_URL."/invest/fail/" . $invest->project . "/" . $invest->id;
                // y la firma
                // Clave_encriptacion+MerchantID+AcquirerBIN+TerminalID+Num_operacion+Importe+Tipo Moneda+Exponente+ +Cadena SHA1+URL_OK+URL_NOK
                $sign_code = $clave.$MerchantID.$AcquirerBIN.$TerminalID.$token.$amount.$currency.$exponent.$cypher.$Url_OK.$Url_NOK;
                $Firma = sha1($sign_code);
                
//                echo 'Carro: '.$sign_code . '<br />Da: ' . $Firma . '<hr />';


                $datos = array(
                    'MerchantID'    => $MerchantID,
                    'AcquirerBIN'   => $AcquirerBIN,
                    'TerminalID'    => $TerminalID,
                    'Num_operacion'	=> $token,
                    'Importe'		=> $amount,
                    'TipoMoneda'	=> $currency,
                    'Exponente'     => $exponent,
                    'URL_OK'        => $Url_OK,
                    'URL_NOK'       => $Url_NOK,
                    'Firma'         => $Firma,
                    'Cifrado'       => $cypher,
                    'Idioma'        => self::$langs[LANG],
                    'Pago_soportado'=> 'SSL',
                    'Descripcion'=> "Aporte de {$invest->amount} EUR al proyecto: " . \utf8_decode($project->name)
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

                $logger->log('##### TPV ['.$invest->id.'] '.date('d/m/Y').' User:'.$_SESSION['user']->id.'#####');

                $logger->log("Charge request: $MsgStr");
                $logger->close();

                echo '<html><head><title>Goteo.org</title></head><body><form action="'.$urlTPV.'" method="post" id="form_tpv" enctype="application/x-www-form-urlencoded">'.$data.'</form><script type="text/javascript">document.getElementById("form_tpv").submit();</script></body></html>';
                return true;
			}
			catch(Exception $ex) {

                $errors[] = 'Error fatal en la comunicación con el TPV, se ha reportado la incidencia. Disculpe las molestias.';
                @mail('goteo-tpv-fault@doukeshi.org', 'Error fatal en comunicacion TPV Sermepa', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>' . $ex->getMessage());
                return false;
			}
            
        }

        public static function cancelPreapproval ($invest, &$errors = array()) {
            return static::cancelPay($invest, $errors);
        }
        public static function cancelPay($invest, &$errors = array()) {

			try {
                if (empty($invest->payment)) {
                    $invest->cancel();
                    return true;
                }

                // preparo los campos
                $MerchantID = TPV_MERCHANT_CODE;
                $AcquirerBIN = '0000554041';
                $TerminalID = '00000003';
                $token = $invest->preapproval;
                $amount = $invest->amount * 100;
                $Reference = $invest->payment;
                $currency = '978';
                $exponent = '2';
                $cypher = 'SHA1';
                $urlMerchant = "http://www.goteo.org";
                $clave = TPV_ENCRYPT_KEY;

                // y la firma para anulaciones
                // Clave_encriptacion+MerchantID+AcquirerBIN+TerminalID+Num_operacion+Importe+TipoMoneda+ Exponente+ Referencia+ Cadena SHA1
                $sign_code = $clave.$MerchantID.$AcquirerBIN.$TerminalID.$token.$amount.$currency.$exponent.$Reference.$cypher;
                $Firma = sha1($sign_code);

//                echo 'Carro: '.$sign_code . '<br />Da: ' . $Firma . '<hr />';


                $datos = array(
                    'MerchantID'    => $MerchantID,
                    'AcquirerBIN'   => $AcquirerBIN,
                    'TerminalID'    => $TerminalID,
                    'Num_operacion'	=> $token,
                    'Importe'		=> $amount,
                    'TipoMoneda'	=> $currency,
                    'Exponente'     => $exponent,
                    'Referencia'    => $Reference,
                    'Firma'         => $Firma,
                    'Cifrado'       => $cypher
                );

                //echo \trace($datos);

                // mandarlo al tpv
                $urlTPV = TPV_REDIRECT_URL . 'anularparcialmente';

                $handler = new \WSHandler();
                $response = $handler->callWebService($datos, $urlTPV);

               if(strtoupper($handler->isSuccess) == 'FAILURE') {
                   $errors[] = 'No se ha podido completado la comunicación con ceca para procesar la anulación del cargo. ';
                    @mail('goteo-tpv-fault@doukeshi.org', 'Fallo en la comunicacion TPV Sermepa', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($errors, 1) . '</pre>');
                   return false;
                } else {
                    $respobj = \htmlentities($response);
                    //echo $respobj . '<hr />';
                    // de cualquier manera el aporte lo cancelamos
                    $invest->cancel();
                    // buscamos el codigo 900 de anulacion realizada correctamente
                    if (\stripos($response, 'REALIZADA') !== false
                        && \strpos($respobj, '900') !== false ) {
                        $errors[] = 'Cargo anulado correctamente';
                        return true;
                    } elseif (\stripos($response, 'ya anulada') !== false) {
                        $errors[] = 'Este cargo ya estaba anulado';
                        return true;
                    } else {
                        $errors[] = 'No se ha podido procesar la anulación del cargo. Localizar la operación <strong>'.$token.'</strong> en el panel tpv. El aporte el aporte <strong>'.$invest->id . '</strong> ha sido cancelado.';
                        @mail('goteo-tpv-fault@doukeshi.org', 'No encuentra codigo en la comunicacion TPV Sermepa', 'ERROR en ' . __FUNCTION__ . '<hr />' . $response . '<pre>'.print_r($datos, 1).'</pre>');
                        return false;
                    }
                }
//                echo implode('<br />', $errors);
//                die;
			}
			catch(Exception $ex) {

                $errors[] = 'Error fatal en la comunicación con el TPV, se ha reportado la incidencia. Disculpe las molestias.';
                @mail('goteo-tpv-fault@doukeshi.org', 'Error fatal en comunicacion TPV Sermepa', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
			}

        }

	}
	
}