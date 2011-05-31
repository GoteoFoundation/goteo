<?php
namespace Goteo\Library {

    use Goteo\Model\Invest,
        Goteo\Core\Redirection;

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

                $MerchantID = TPV_MERCHANT_CODE;
                $currency = '978';
                $transactionType = 0;
//                $urlMerchant = SITE_URL."/tpv/comunication";
                $urlMerchant = "http://facturaweb.onliners-web.com/goteo/tpv.php";
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
                     'Ds_Merchant_MerchantData'     => 'InvestId='.$invest->id
                );

                // Guardar el codigo de preaproval en el registro de aporte y mandarlo al tpv
                $invest->setPreapproval($token);
                $urlTPV = TPV_REDIRECT_URL;
                $data = '';
                foreach ($datos as $n => $v) {
                    $data .= '<input name="'.$n.'" type="hidden" value="'.$v.'" />';
                }

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

            return true;

            try {
                // obtener transaccion y grabarla en el registro de aporte
            }
            catch (Exception $e) {
                $errors[] = 'Error fatal en la comunicación con tpv, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-tpv-fault@doukeshi.org', 'Error fatal en comunicacion tpv', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }

        }


        /*
         * Llamada a tpv para obtener los detalles de un preapproval
         */
        public static function preapprovalDetails ($key, &$errors = array()) {
            try {
                return 'tpvCODE';
            }
            catch(Exception $ex) {
                $errors[] = 'Error fatal en la comunicación con tpv, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-tpv-fault@doukeshi.org', 'Error fatal en comunicacion tpv', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }
        }

        /*
         * Llamada a tpv para obtener los detalles de un cargo
         */
        public static function paymentDetails ($key, &$errors = array()) {
            try {
                return 'tpcTRANSACTIONid';
            }
            catch(Exception $ex) {
                $errors[] = 'Error fatal en la comunicación con tpv, se ha reportado la incidencia. Disculpe las molestias.';
                @\mail('goteo-tpv-fault@doukeshi.org', 'Error fatal en comunicacion tpv', 'ERROR en ' . __FUNCTION__ . '<br /><pre>' . print_r($fault, 1) . '</pre>');
                return false;
            }
        }


        /*
         * Llamada para cancelar un preapproval (si llega a los 40 sin conseguir el mínimo)
         * recibe la instancia del aporte
         */
        public static function cancelPreapproval ($invest, &$errors = array()) {
            try {
                $invest->cancelPreapproval();
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