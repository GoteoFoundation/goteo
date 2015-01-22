<?php

namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Library\Mail,
        Goteo\Library\Paypal;

    class Cron extends \Goteo\Core\Controller {

        /**
         *
         */
        public function index () {
            die('bad request');
        }

        /**
         *  Proceso que ejecuta los cargos, cambia estados, lanza eventos de cambio de ronda
         */
        public function execute () {
            // debug para supervisar en las fechas clave
            // $debug = ($_GET['debug'] == 'debug') ? true : false;
            $debug = true;
            $start = $this->get_microtime();

            if ($debug) echo '<strong>cron/execute start</strong><br />';

            if (!\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado MANUALMENTE el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
                echo 'Lanzamiento manual a las ' . date ('H:i:s') . ' <br />';
            } else {
                echo 'Lanzamiento automatico a las ' . date ('H:i:s') . ' <br />';
            }

            // a ver si existe el bloqueo (PARA HOY)
            $block_file = GOTEO_LOG_PATH . 'cron-'.__FUNCTION__.'_'.date('Ymd').'.block';

            if ( $this->cron_lock($block_file, 'execute') ) {

                Cron\Execute::process($debug);

                echo '<hr />';

                $finish = $this->get_microtime();
                $total_time = round(($finish - $start), 4);

                if ($debug) {
                    echo '<hr/>';
                    echo "<br /><strong>cron/execute finish (executed in ".$total_time." seconds)</strong><hr />";
                }

                // desbloqueamos
                $this->cron_unlock($block_file, 'execute');

            }

            // recogemos el buffer para grabar el log
            @mkdir(GOTEO_LOG_PATH . 'cron/', 0777, true);
            $log_file = GOTEO_LOG_PATH . 'cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0666);
        }


        /*
         *  Proceso que verifica si los preapprovals han sido coancelados
         *   Solamente trata transacciones paypal pendientes de proyectos en campaña
         *
         */
        public function verify () {
            if (!\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
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

        /*
         *  Proceso que limpia la tabla de imágenes
         * y también limpia el directorio
         *
         */
        public function cleanup () {
            if (\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se intentaba lanzar automáticamente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
               die;
            } else {

                Cron\Cleanup::process();
                die();
            }
        }

        /*
         *  Proceso para tratar los geologins
         *
         */
        public function geoloc () {
            // no necesito email de aviso por el momento
            /*
            if (!\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
               echo 'Lanzamiento manual<br />';
            } else {
               echo 'Lanzamiento automatico<br />';
            }
            */

            // lanzamos subcontrolador
            Cron\Geoloc::process();

            // Por el momento no grabamos log de esto, lo lanzamos manual
            /*
            @mkdir(GOTEO_LOG_PATH . 'cron/', 0777, true);
            $log_file = GOTEO_LOG_PATH . 'cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0666);
             */

            die();
        }

        /*
         * DEPRECATED. No se usa ya.
         * Realiza los pagos secundarios al proyecto
         *
         * Esto son los aportes de tipo paypal, ejecutados (status 1), que tengan payment code
         *
         */
        public function dopay ($project) {
            die('Ya no realizamos pagos secundarios mediante sistema');
            if (\defined('CRON_EXEC')) {
                die('Este proceso no necesitamos lanzarlo automaticamente');
            }

            @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                'Se ha lanzado manualmente el cron '. __FUNCTION__ .' para el proyecto '.$project.' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);

            // a ver si existe el bloqueo
            $block_file = GOTEO_LOG_PATH . 'cron-'.__FUNCTION__.'.block';
            if (file_exists($block_file)) {
                echo 'Ya existe un archivo de log '.date('Ymd').'_'.__FUNCTION__.'.log<br />';
                $block_content = \file_get_contents($block_file);
                echo 'El contenido del bloqueo es: '.$block_content;
                // lo escribimos en el log
                @mkdir(GOTEO_LOG_PATH . 'cron/', 0777, true);
                $log_file = GOTEO_LOG_PATH . 'cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
                \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
                \chmod($log_file, 0666);
                /*
                @mail(\GOTEO_FAIL_MAIL, 'Cron '. __FUNCTION__ .' bloqueado en ' . SITE_URL,
                    'Se ha encontrado con que el cron '. __FUNCTION__ .' está bloqueado el '.date('d-m-Y').' a las ' . date ('H:i:s') . '
                        El contenido del bloqueo es: '. $block_content);
                 */
                die;
            } else {
                $block = 'Bloqueo '.$block_file.' activado el '.date('d-m-Y').' a las '.date ('H:i:s').'<br />';
                if (\file_put_contents($block_file, $block, FILE_APPEND)) {
                    \chmod($block_file, 0777);
                    echo $block;
                } else {
                    echo 'No se ha podido crear el archivo de bloqueo<br />';
                    @mail(\GOTEO_FAIL_MAIL, 'Cron '. __FUNCTION__ .' no se ha podido bloquear en ' . SITE_URL,
                        'No se ha podido crear el archivo '.$block_file.' el '.date('d-m-Y').' a las ' . date ('H:i:s'));
                }
            }

            $projectData = Model\Project::getMini($project);

            // necesitamos la cuenta del proyecto y que sea la misma que cuando el preapproval
            $projectAccount = Model\Project\Account::get($project);

            if (empty($projectAccount->paypal)) {
                // iniciamos mail
                $mailHandler = new Mail();
                $mailHandler->to = \GOTEO_MAIL;
                $mailHandler->toName = 'Goteo.org';
                $mailHandler->subject = 'El proyecto '.$projectData->name.' no tiene cuenta PayPal';
                $mailHandler->content = 'Hola Goteo, el proyecto '.$projectData->name.' no tiene cuenta PayPal y se estaba intentando realizar pagos secundarios.';
                $mailHandler->html = false;
                $mailHandler->template = null;
                $mailHandler->send();
                unset($mailHandler);

                die('El proyecto '.$projectData->name.' no tiene la cuenta PayPal!!');
            }

            // tratamiento de aportes pendientes
            $query = Model\Project::query("
                SELECT  *
                FROM  invest
                WHERE   invest.status = 1
                AND     invest.method = 'paypal'
                AND     invest.project = ?
                ", array($project));
            $invests = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');

            echo 'Vamos a tratar ' . count($invests) . ' aportes para el proyecto '.$projectData->name.'<br />';

            foreach ($invests as $key=>$invest) {
                $errors = array();

                $userData = Model\User::getMini($invest->user);
                echo 'Tratando: Aporte (id: '.$invest->id.') de '.$userData->name.' ['.$userData->email.']<br />';

                if (Paypal::doPay($invest, $errors)) {
                    echo 'Aporte (id: '.$invest->id.') pagado al proyecto. Ver los detalles en la <a href="/admin/accounts/details/'.$invest->id.'">gestion de transacciones</a><br />';
                    $log_text = "Se ha realizado el pago de %s PayPal al proyecto %s por el aporte de %s (id: %s) del dia %s";
                    Model\Invest::setDetail($invest->id, 'payed', 'Se ha realizado el pago secundario al proyecto. Proceso cron/doPay');

                } else {
                    echo 'Fallo al pagar al proyecto el aporte (id: '.$invest->id.'). Ver los detalles en la <a href="/admin/accounts/details/'.$invest->id.'">gestion de transacciones</a><br />' . implode('<br />', $errors);
                    $log_text = "Ha fallado al realizar el pago de %s PayPal al proyecto %s por el aporte de %s (id: %s) del dia %s";
                    Model\Invest::setDetail($invest->id, 'pay-failed', 'Fallo al realizar el pago secundario: ' . implode('<br />', $errors) . '. Proceso cron/doPay');
                }

                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Pago al proyecto encadenado-secundario (cron)', '/admin/accounts',
                    \vsprintf($log_text, array(
                    Feed::item('money', $invest->amount.' &euro;'),
                    Feed::item('project', $projectData->name, $project),
                    Feed::item('user', $userData->name, $userData->id),
                    Feed::item('system', $invest->id),
                    Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                )));
                $log->doAdmin();
                unset($log);

                echo '<hr />';
            }

            // desbloqueamos
            if (unlink($block_file)) {
                echo 'Cron '. __FUNCTION__ .' desbloqueado<br />';
            } else {
                echo 'ALERT! Cron '. __FUNCTION__ .' no se ha podido desbloquear<br />';
                if(file_exists($block_file)) {
                    echo 'El archivo '.$block_file.' aun existe!<br />';
                } else {
                    echo 'No hay archivo de bloqueo '.$block_file.'!<br />';
                }
            }

            // recogemos el buffer para grabar el log
            @mkdir(GOTEO_LOG_PATH . 'cron/', 0777, true);
            $log_file = GOTEO_LOG_PATH . 'cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0666);
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
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
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


        /**
         *  Proceso que arregla las extensiones de los archivos de imágenes
         */
        public function imgrename () {
            if (\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se intentaba lanzar automáticamente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
               die;
            } else {

                Cron\Imgrename::process();
                die();
            }
        }


        /**
         * Unlock
         */
        private function cron_lock($block_file, $cron_name) {
            if (file_exists($block_file)) {
                $block_content = \file_get_contents($block_file);
                echo 'Ya existe un archivo de log '.date('Ymd').'_'.$cron_name.'.log<br />';
                echo 'El contenido del bloqueo es: '.$block_content;

                // lo escribimos en el log
                @mkdir(GOTEO_LOG_PATH . 'cron/', 0777, true);
                $log_file = GOTEO_LOG_PATH . 'cron/'.date('Ymd').'_'.$cron_name.'.log';
                \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
                \chmod($log_file, 0666);

                @mail(\GOTEO_FAIL_MAIL, 'Cron '. $cron_name .' bloqueado en ' . SITE_URL,
                    'Se ha encontrado con que el cron '. $cron_name .' está bloqueado el '.date('d-m-Y').' a las ' . date ('H:i:s') . '
                        El contenido del bloqueo es: '. $block_content);

                return false;

            } else {
                $block = 'Bloqueo del '.$block_file.' activado el '.date('d-m-Y').' a las '.date ('H:i:s').'<br />';
                if (\file_put_contents($block_file, $block, FILE_APPEND)) {
                    \chmod($block_file, 0777);
                    echo $block;

                    return true;
                } else {
                    echo 'No se ha podido crear el archivo de bloqueo<br />';
                    @mail(\GOTEO_FAIL_MAIL, 'Cron '. $cron_name .' no se ha podido bloquear en ' . SITE_URL,
                        'No se ha podido crear el archivo '.$block_file.' el '.date('d-m-Y').' a las ' . date ('H:i:s'));

                    return false;
                }
            }
        }


        /**
         * Unlock
         */
        private function cron_unlock($block_file, $cron_name) {
            if (unlink($block_file)) {
                echo 'Cron '. $cron_name .' desbloqueado<br />';
            } else {
                echo 'ALERT! Cron '. $cron_name .' no se ha podido desbloquear<br />';
                if(file_exists($block_file)) {
                    echo 'El archivo '.$block_file.' aun existe!<br />';
                } else {
                    echo 'No hay archivo de bloqueo '.$block_file.'!<br />';
                }
                @mail(\GOTEO_FAIL_MAIL, 'Cron '. $cron_name .' no se ha podido desbloquear en ' . SITE_URL,
                    'No se ha podido eliminar el archivo '.$block_file.' el '.date('d-m-Y').' a las ' . date ('H:i:s'));
            }
        }


        /**
         * Función para calcular lo que tarda el cron
         */
        private function get_microtime() {
            $time = microtime();
            $time = explode(' ', $time);
            $time = $time[1] + $time[0];
            return $time;
        }
    }

}
