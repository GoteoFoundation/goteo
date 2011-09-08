<?php

namespace Goteo\Controller {

    use Goteo\Model\Invest,
        Goteo\Core\Error,
        Goteo\Core\Redirection;

    require_once 'library/paypal/stub.php'; // sí, uso el stub de paypal
    require_once 'library/paypal/log.php'; // sí, uso el log de paypal
    
    class Tpv extends \Goteo\Core\Controller {
        
        public function index () {
            throw new Redirection('/', Error::BAD_REQUEST);
        }
        

        public function comunication () {
            if (isset($_POST['Ds_Order'])) {
                $_POST['invest'] = $id = \substr($_POST['Ds_Order'], 0, -4);
                
                $invest = Invest::get($id);

                if (empty($_POST['Ds_ErrorCode'])) {
                    $invest->setTransaction($_POST['Ds_AuthorisationCode']);
                    $_POST['result'] = 'Transaccion ok';
                    $invest->setStatus('0');
                } else {
                    if ($_POST['Ds_ErrorCode'] == 'SIS0257') {
                        @\mail('hola@goteo.org', 'Ojo, esta tarjeta no permite preautorizaciones', 'Intentan aportar con una tarjeta que no permite preautorizaciones<br /><pre>' . print_r($_POST, 1) . '</pre>');
                        @\mail('goteo-tpv-fault@doukeshi.org', 'Tarjeta no permite preautorizaciones', 'Intentan aportar con una tarjeta que no permite preautorizaciones<br /><pre>' . print_r($_POST, 1) . '</pre>');
                    }
                    $invest->cancel($_POST['Ds_ErrorCode']);
                    $_POST['result'] = 'Fail';
                }

                $response = '';
                foreach ($_POST as $n => $v) {
                    $response .= "{$n}:'{$v}'; ";
                }

                $conf = array('mode' => 0600, 'timeFormat' => '%X %x');
                $logger = &\Log::singleton('file', 'logs/'.date('Ymd').'_invest.log', 'caller', $conf);

                $logger->log("response: $response");
                $logger->log('##### END TPV ['.$id.'] '.date('d/m/Y').' '.$_POST['Ds_MerchantData'].'#####');
                $logger->close();
            } else {
                throw new Redirection('/', Error::BAD_REQUEST);
            }

            
        }

        public function simulacrum () {
            echo 'Simulacrum<br />';
            echo \trace($_POST);
            die;
        }

    }
    
}