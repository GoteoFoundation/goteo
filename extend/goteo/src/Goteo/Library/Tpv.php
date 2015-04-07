<?php
namespace Goteo\Library {

    use Goteo\Model\Invest,
        Goteo\Model\Project,
        Goteo\Library\Text,
        Goteo\Core\Redirection;

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

        /*
         * metodo para mantener coherencia
         */
        public static function execute($invest, &$errors = array()) {
            return true;
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

                $Url_OK  = $invest->urlOK;
                $Url_NOK = $invest->urlNOK."/?amount=".$invest->amount;
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

                // LOGGER
                Feed::logger('tpv request', 'invest', $invest->id, $MsgStr, SITE_URL.$_SERVER['REQUEST_URI']);

                Invest::setDetail($invest->id, 'tpv-conection', 'Ha iniciado la comunicacion con el tpv, operacion numero ' . $token . '. Proceso libary/tpv::pay');

                echo '<html>
                <head>
                    <title>Goteo.org</title>
                    <noscript>'.Text::get('warning_nojavascript').'<br /><a href="/project/'.$invest->project.'/invest/?amount='.$invest->amount.'">CONTINUE</a></noscript>
                </head>
                <body>
                <form action="'.$urlTPV.'" method="post" id="form_tpv" enctype="application/x-www-form-urlencoded">'.$data.'</form>
                <script type="text/javascript">document.getElementById("form_tpv").submit();</script>
                </body>
                </html>';
                return true;
			}
			catch(Exception $ex) {

                Invest::setDetail($invest->id, 'tpv-conection-fail', 'Ha fallado la comunicacion con el tpv. Proceso libary/tpv::pay');
                $errors[] = 'Error fatal en la comunicacion con el TPV, se ha reportado la incidencia. Disculpe las molestias.';
                @mail(\GOTEO_FAIL_MAIL, 'Error fatal en comunicacion TPV  en ' . SITE_URL, 'ERROR en ' . __FUNCTION__ . '<br />' . $ex->getMessage());
                return false;
			}

        }

        public static function cancelPreapproval ($invest, &$errors = array(), $fail = false) {
            return static::cancelPay($invest, $errors, $fail);
        }
        public static function cancelPay($invest, &$errors = array(), $fail = false) {

			try {
                if (empty($invest->payment)) {
                    $invest->cancel($fail);
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

                $handler = new Tpv\Handler();
                $response = $handler->callWebService($datos, $urlTPV);

               if(strtoupper($handler->isSuccess) == 'FAILURE') {
                    Invest::setDetail($invest->id, 'tpv-cancel-conection-fail', 'Ha fallado la comunicacion con el tpv al anular la operacion. Proceso libary/tpv::cancelPay');
                    $errors[] = 'No se ha podido completar la comunicacion con ceca para procesar la anulación del cargo. ';
                    @mail(\GOTEO_FAIL_MAIL, 'Fallo en la comunicacion TPV  en ' . SITE_URL, 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($errors, true) . '</pre>');
                   return false;
                } elseif (!empty($response)) {
                    $respobj = \htmlentities($response);
                    //echo $respobj . '<hr />';
                    // buscamos el codigo 900 de anulacion realizada correctamente
                    if (\stripos($response, 'REALIZADA') !== false
                        && \strpos($respobj, '900') !== false ) {
                        Invest::setDetail($invest->id, 'tpv-cancel', 'Se ha anulado la operacion de tpv. Proceso libary/tpv::cancelPay');
                        $errors[] = 'Cargo anulado correctamente';
                        // cancelamos aporte
                        $invest->cancel($fail);
                        return true;
                    } elseif (\stripos($response, 'success') !== false
                        && \strpos($respobj, 'anulaoperacionok') !== false ) {
                        Invest::setDetail($invest->id, 'tpv-cancel', 'Se ha anulado la operacion de tpv. Proceso libary/tpv::cancelPay');
                        $errors[] = 'Cargo anulado correctamente';
                        // cancelamos aporte
                        $invest->cancel($fail);
                        return true;
                    } elseif (\stripos($response, 'ya anulada') !== false) {
                        Invest::setDetail($invest->id, 'tpv-cancel', 'Se intentaba anular por segunda vez la operacion de tpv. Proceso libary/tpv::cancelPay');
                        $errors[] = 'Este cargo ya estaba anulado';
                        // cancelamos aporte
                        $invest->cancel($fail);
                        return true;
                    } else {
                        Invest::setDetail($invest->id, 'tpv-cancel-fail', 'No se ha podido anular la operacion de tpv. Proceso libary/tpv::cancelPay');
                        $errors[] = 'No se ha podido procesar la anulación del cargo. Localizar la operación <strong>'.$token.'</strong> en el panel tpv. El aporte el aporte <strong>'.$invest->id . '</strong> ha sido cancelado.';
                        @mail(\GOTEO_FAIL_MAIL, 'No encuentra codigo en la comunicacion TPV Sermepa', 'ERROR en ' . __FUNCTION__ . '<hr />' . $response . '<pre>'.print_r($datos, true).'</pre>');
                        return false;
                    }
                } else {
                    @mail(\GOTEO_FAIL_MAIL, 'Error fatal en comunicacion TPV  en ' . SITE_URL, 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($handler, true) . '</pre>');
                    return false;
                }
			}
			catch(Exception $ex) {
                Invest::setDetail($invest->id, 'tpv-cancel-conection-fail', 'Ha fallado la comunicacion con el tpv al anular la operacion. Proceso libary/tpv::cancelPay');
                $errors[] = 'Error fatal en la comunicación con el TPV, se ha reportado la incidencia. Disculpe las molestias.';
                @mail(\GOTEO_FAIL_MAIL, 'Error fatal en comunicacion TPV  en ' . SITE_URL, 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($handler, true) . '</pre>');
                return false;
			}

        }

	}

}
