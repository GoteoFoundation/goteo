<?php

namespace Goteo\Controller {

    use Goteo\Model\Invest,
        Goteo\Model\Project,
        Goteo\Model\User,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Core\Redirection;

    require_once 'library/paypal/stub.php'; // sí, uso el stub de paypal
    require_once 'library/paypal/log.php'; // sí, uso el log de paypal
    
    class Tpv extends \Goteo\Core\Controller {

        public static $errcode = array(
            'SIS0257' => 'Tarjeta no permite preapproval',
            'SIS0253' => 'Tarjeta no reconocida',
            'SIS0051' => 'Pedido repetido',
            'SIS0078' => 'Método de pago no disponible para su tarjeta',
            'SIS0093' => 'Tarjeta no válida',
            'SIS0094' => 'Error en la llamada al MPI sin controlar',
            'SIS0218' => 'El comercio no permite preatorizacion por la entrada XML',
            'SIS0256' => 'El comercio no puede realizar preautorizaciones',
            'SIS0257' => 'Esta tarjeta no permite operativa de preautorizaciones',
            'SIS0261' => 'Operación detenida por superar el control de restricciones en la entrada al SIS',
            'SIS0270' => 'El comercio no puede realizar autorizaciones en diferido',
            'SIS0274' => 'Tipo de operación desconocida o no permitida por esta entrada al SIS'
        );

        /*
        (ds_response) tendrá los siguientes valores posibles:
        0000 a 0099 Transacción autorizada para pagos y preautorizaciones
        Cualquier otro valor (que no esté en el array) Transacción denegada
         */
        public static $respcode = array(
            '0900' => 'Transacción autorizada para devoluciones y confirmaciones',
            '9104' => 'Operación no permitida para esa tarjeta o terminal',
            '9912' => 'Emisor no disponible',
            '101' => 'Tarjeta caducada',
            '102' => 'Tarjeta en excepción transitoria o bajo sospecha de fraude',
            '104' => 'Operación no permitida para esa tarjeta o terminal',
            '116' => 'Disponible insuficiente',
            '118' => 'Tarjeta no registrada (Método de pago no disponible para su tarjeta)',
            '129' => 'Código de seguridad (CVV2/CVC2) incorrecto',
            '180' => 'Tarjeta ajena al servicio (Tarjeta no válida)',
            '184' => 'Error en la autenticación del titular (Error en la llamada al MPI sin controlar)',
            '190' => 'Denegación sin especificar Motivo',
            '191' => 'Fecha de caducidad errónea',
            '202' => 'Tarjeta en excepción transitoria o bajo sospecha de fraude con retirada de tarjeta',
            '913' => 'Pedido repetido',
            '912' => 'Emisor no disponible'
        );

        public function index () {
            throw new Redirection('/', Error::BAD_REQUEST);
        }
        

        public function comunication () {
            if (isset($_POST['Ds_Order'])) {
                $_POST['invest'] = $id = \substr($_POST['Ds_Order'], 0, -4);
                
                $invest = Invest::get($id);

                $userData = User::getMini($invest->user);
                $projectData = Project::getMini($invest->project);

                // a ver si hay una respuesta chunga
                $Cresp = (string) $_POST['Ds_Response'];
                $respTxt = self::$respcode[$Cresp];
                if ($_POST['Ds_Response'] > 99 && !empty($respTxt)) {
                    
                    @\mail('goteo-tpv-fault@doukeshi.org', 'Respuesta de Fallo', $respTxt.'<br /><pre>' . print_r($_POST, 1) . '</pre>');
                    $invest->cancel('RESP' . $Cresp);
                    $_POST['result'] = 'Fail';

                    $log_text = "Ha habido una respuesta de Fallo de TPV (Codigo: {$Cresp}: ".$respTxt.") en el aporte de %s de %s al proyecto %s mediante TPV";

                } elseif (empty($_POST['Ds_ErrorCode'])) {
                    
                    $invest->setTransaction($_POST['Ds_AuthorisationCode']);
                    $_POST['result'] = 'Transaccion ok';
                    $invest->setStatus('0');

                    $log_text = "%s ha aportado %s al proyecto %s mediante TPV";

                } else {

                    $Cerr = (string) $_POST['Ds_ErrorCode'];
                    $errTxt = self::$errcode[$Cerr];
                    @\mail('goteo-tpv-fault@doukeshi.org', 'Error en TPV', $errTxt.'<br /><pre>' . print_r($_POST, 1) . '</pre>');
                    $invest->cancel($_POST['Ds_ErrorCode']);
                    $_POST['result'] = 'Fail';

                    $log_text = "Ha habido un error de TPV (Codigo: {$Cerr}: ".$errTxt.") en el aporte de %s de %s al proyecto %s mediante TPV";

                }

                /*
                 * Evento Feed
                 */
                $log = new Feed();
                $log->title = 'Aporte TPV';
                $log->url = '/admin/invests';
                $log->type = 'money';
                $items = array(
                    Feed::item('user', $userData->name, $userData->id),
                    Feed::item('money', $invest->amount.' &euro;'),
                    Feed::item('project', $projectData->name, $projectData->id)
                );
                $log->html = \vsprintf($log_text, $items);
                $log->add($errors);
                unset($log);

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