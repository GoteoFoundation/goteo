<?php

namespace Goteo\Controller {

    use Goteo\Model\Invest,
        Goteo\Core\Error,
        Goteo\Core\Redirection;

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
                } else {
                    $invest->cancel($_POST['Ds_ErrorCode']);
                    $_POST['result'] = 'Fail';
                }

                @\mail( 'jcanaves@doukeshi.org', 'Comunicacion online goteo desde goteo.org/tpv/comunication', '<pre>'.print_r($_POST, 1).'</pre>');
            }

            
        }

        public function simulacrum () {
            echo 'Simulacrum<br />';
            echo \trace($_POST);
            die;
        }

    }
    
}