<?php

namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Application\Session,
        Goteo\Library\Feed,
        Goteo\Library\Mail,
        Goteo\Library\Paypal;

    class Cron extends \Goteo\Core\Controller {


        /*
         *  Proceso que verifica si los preapprovals han sido coancelados
         *   Solamente trata transacciones paypal pendientes de proyectos en campaña
         *
         */
        public function verify () {
            if (!\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. Session::getUserId());
               echo 'Lanzamiento manual<br />';
            } else {
               echo 'Lanzamiento automatico<br />';
            }

            $debug = (isset($_GET['debug']) && $_GET['debug'] == 'debug') ? true : false;
            if ($debug) echo 'Modo debug activado<hr />';

            // lanzamos subcontrolador
            Cron\Verify::process($debug);

            // recogemos el buffer para grabar el log
            /*
            @mkdir(GOTEO_LOG_PATH . 'cron/', 0777, true);
            $log_file = GOTEO_LOG_PATH . 'cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0666);
            */
            die();
        }


        /**
         *  Proceso para enviar avisos a los autores segun
         *  - Que lleven 3 meses sin publicar actualizaciones, envia cada mes
         *  - Que lleven 3 meses sin decir nada (?), envia cada 15 dias
         *  - Que hayan pasado dos meses desde que se dio por financiado, cada 15 dias
         *
         *  tiene en cuenta que se envía cada tantos días
         */

        public function daily () {
            if (!\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. Session::getUserId());
//                die('Este proceso no necesitamos lanzarlo manualmente');
            }

            // temporalmente debug fijo (quitarlo al quitar monitorización)
//            $debug = (isset($_GET['debug']) && $_GET['debug'] == 'debug') ? true : false;
            $debug = true;

            if ($debug) echo 'Modo debug activado<hr />';

            // subcontrolador Auto-tips
            Cron\Daily::Projects($debug);

            // subcontrolador progreso convocatorias
            Cron\Daily::Calls($debug);


            if ($debug) {
                // recogemos el buffer para grabar el log
                @mkdir(GOTEO_LOG_PATH . 'cron/', 0777, true);
                $log_file = GOTEO_LOG_PATH . 'cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
                \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
                \chmod($log_file, 0666);
            }
        }

    }

}
