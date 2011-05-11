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
            
			try {
		           $returnURL = SITE_URL."/invest/confirmed/" . $invest->project; // a difundirlo @TODO mensaje gracias si llega desde un preapproval
		           $cancelURL = SITE_URL."/invest/fail/" . $invest->project . "/" . $invest->id; // a la página de aportar para intentarlo de nuevo

                    // desde hoy hasta 45 dias
                   /*
                    date_default_timezone_set('UTC');
                    $currDate = getdate();
                    $hoy = $currDate['year'].'-'.$currDate['mon'].'-'.$currDate['mday'];
                    $startDate = strtotime($hoy);
                    $startDate = date('Y-m-d', mktime(date('h',$startDate),date('i',$startDate),0,date('m',$startDate),date('d',$startDate),date('Y',$startDate)));
                    $endDate = strtotime($hoy);
                    $endDate = date('Y-m-d', mktime(0,0,0,date('m',$endDate),date('d',$endDate)+45,date('Y',$endDate)));
                    */


                    // Guardar el codigo de preaproval en el registro de aporte y mandarlo al tpv
                    $token = 'tpvCODE';
                    if (!empty($token)) {
                        $invest->setPreapproval($token);
                        $tpvURL = TPV_REDIRECT_URL;
                        // escribir el formulario sermepa y enviar
                        die('Hola tpv');
                    } else {
                        $errors[] = 'No tpv code obtained. <pre>' . print_r($response, 1) . '</pre>';
                        return false;
                    }

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