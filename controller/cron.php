<?php

namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Library\Text,
        Goteo\Library\Feed,
        Goteo\Library\Template,
        Goteo\Library\Mail,
        Goteo\Library\Paypal,
        Goteo\Library\Tpv;

    class Cron extends \Goteo\Core\Controller {
        
        public function index () {
            die('bad request');
        }

        /*
         *  Proceso que ejecuta los cargos, cambia estados, lanza eventos de cambio de ronda
         */
        public function execute () {

            if (!\defined('CRON_EXEC')) {
                @mail('goteo_cron@doukeshi.org', 'Se ha lanzado MANUALMENTE el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
               echo 'Lanzamiento manual a las ' . date ('H:i:s') . ' <br />';
            } else {
                echo 'Lanzamiento automatico a las ' . date ('H:i:s') . ' <br />';
            }
            
            // a ver si existe el bloqueo
            $block_file = GOTEO_PATH.'logs/cron-'.__FUNCTION__.'.block';
            if (file_exists($block_file)) {
                echo 'Ya existe un archivo de log '.date('Ymd').'_'.__FUNCTION__.'.log<br />';
                $block_content = \file_get_contents($block_file);
                echo 'El contenido del bloqueo es: '.$block_content;
                // lo escribimos en el log
                $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
                \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
                \chmod($log_file, 0777);
                @mail('goteo_cron@doukeshi.org', 'Cron '. __FUNCTION__ .' bloqueado en ' . SITE_URL,
                    'Se ha encontrado con que el cron '. __FUNCTION__ .' está bloqueado el '.date('d-m-Y').' a las ' . date ('H:i:s') . '
                        El contenido del bloqueo es: '. $block_content);
                die;
            } else {
                $block = 'Bloqueo del '.$block_file.' activado el '.date('d-m-Y').' a las '.date ('H:i:s').'<br />';
                if (\file_put_contents($block_file, $block, FILE_APPEND)) {
                    \chmod($block_file, 0777);
                    echo $block;
                } else {
                    echo 'No se ha podido crear el archivo de bloqueo<br />';
                    @mail('goteo_cron@doukeshi.org', 'Cron '. __FUNCTION__ .' no se ha podido bloquear en ' . SITE_URL,
                        'No se ha podido crear el archivo '.$block_file.' el '.date('d-m-Y').' a las ' . date ('H:i:s'));
                }
            }
            echo '<hr />';
            
            // debug para supervisar en las fechas clave
//            $debug = ($_GET['debug'] == 'debug') ? true : false;
            $debug = true;

            // revision de proyectos: dias, conseguido y cambios de estado
            // proyectos en campaña,
            // (publicados hace más de 40 días que no tengan fecha de pase)
            // o (publicados hace mas de 80 días que no tengan fecha de exito)
            $projects = Model\Project::getActive();

            if ($debug) echo 'Comenzamos con los proyectos en campaña<br /><br />';

            foreach ($projects as $project) {

                if ($debug) echo 'Proyecto '.$project->name.'<br />';

                // a ver si tiene cuenta paypal
                $projectAccount = Model\Project\Account::get($project->id);

                if (empty($projectAccount->paypal)) {

                    if ($debug) echo 'No tiene cuenta PayPal<br />';

                    // Evento Feed solamente si automático
                    if (\defined('CRON_EXEC')) {
                        $log = new Feed();
                        $log->setTarget($project->id);
                        $log->populate('proyecto sin cuenta paypal (cron)', '/admin/projects',
                            \vsprintf('El proyecto %s aun no ha puesto su %s !!!', array(
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('relevant', 'cuenta PayPal')
                        )));
                        $log->doAdmin('project');
                        unset($log);

                        // mail de aviso
                        $mailHandler = new Mail();
                        $mailHandler->to = \GOTEO_CONTACT_MAIL;
                        $mailHandler->toName = 'Goteo.org';
                        $mailHandler->subject = 'El proyecto '.$project->name.' no tiene cuenta PayPal';
                        $mailHandler->content = 'Hola Goteo, el proyecto '.$project->name.' no tiene cuenta PayPal y el proceso automatico no podrá tratar los preaprovals al final de ronda.';
                        $mailHandler->html = false;
                        $mailHandler->template = null;
                        $mailHandler->send();
                        unset($mailHandler);

                        $task = new Model\Task();
                        $task->node = \GOTEO_NODE;
                        $task->text = "Poner la cuenta PayPal al proyecto <strong>{$project->name}</strong> urgentemente!";
                        $task->url = "/admin/projects/accounts/{$project->id}";
                        $task->done = null;
                        $task->save();

                    }

                }

                $log_text = null;

                if ($debug) echo 'Minimo: '.$project->mincost.' &euro; <br />';
                
                $execute = false;
                $cancelAll = false;

                if ($debug) echo 'Obtenido: '.$project->amount.' &euro;<br />';

                // porcentaje alcanzado
                if ($project->mincost > 0) {
                    $per_amount = \round(($project->amount / $project->mincost) * 100);
                } else {
                    $per_amount = 0;
                }
                if ($debug) echo 'Ha alcanzado el '.$per_amount.' &#37; del minimo<br />';

                // los dias que lleva el proyecto  (ojo que los financiados llevaran mas de 80 dias)
                $days = $project->daysActive();
                if ($debug) echo 'Lleva '.$days.'  dias desde la publicacion<br />';

                /* Verificar si enviamos aviso */
                $rest = $project->days;
                $round = $project->round;
                if ($debug) echo 'Quedan '.$rest.' dias para el final de la '.$round.'a ronda<br />';


                // a los 5, 3, 2, y 1 dia para finalizar ronda
                if ($round > 0 && in_array((int) $rest, array(5, 3, 2, 1))) {
                    if ($debug) echo 'Feed publico cuando quedan 5, 3, 2, 1 dias<br />';

                    // Evento Feed solo si ejecucion automática
                    if (\defined('CRON_EXEC')) {
                        $log = new Feed();
                        $log->setTarget($project->id);
                        $log->populate('proyecto próximo a finalizar ronda (cron)', '/admin/projects',
                            Text::html('feed-project_runout',
                                Feed::item('project', $project->name, $project->id),
                                $rest,
                                $round
                        ));
                        $log->doAdmin('project');

                        // evento público
                        $log->title = $project->name;
                        $log->url = null;
                        $log->doPublic('projects');

                        unset($log);
                    }
                }

                //  (financiado a los 80 o cancelado si a los 40 no llega al minimo)
                // si ha llegado a los 40 dias: mínimo-> ejecutar ; no minimo proyecto y todos los preapprovals cancelados
                if ($days >= 40) {
                    // si no ha alcanzado el mínimo, pasa a estado caducado
                    if ($project->amount < $project->mincost) {
                        if ($debug) echo 'Ha llegado a los 40 dias de campaña sin conseguir el minimo, no pasa a segunda ronda<br />';

                        echo $project->name . ': ha recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . '<br />';
                        echo 'No ha conseguido el minimo, cancelamos todos los aportes y lo caducamos:';
                        $cancelAll = true;
                        $errors = array();
                        if ($project->fail($errors)) {
                            $log_text = 'El proyecto %s ha %s obteniendo %s';
                        } else {
                            @mail('goteo_fail@doukeshi.org',
                                'Fallo al archivar ' . SITE_URL,
                                'Fallo al marcar el proyecto '.$project->name.' como archivado ' . implode(',', $errors));
                            echo 'ERROR::' . implode(',', $errors);
                            $log_text = 'El proyecto %s ha fallado al, %s obteniendo %s';
                        }
                        echo '<br />';
                        
                        // Evento Feed solo si ejecucion automatica
                        if (\defined('CRON_EXEC')) {
                            $log = new Feed();
                            $log->setTarget($project->id);
                            $log->populate('proyecto archivado (cron)', '/admin/projects',
                                \vsprintf($log_text, array(
                                    Feed::item('project', $project->name, $project->id),
                                    Feed::item('relevant', 'caducado sin éxito'),
                                    Feed::item('money', $project->amount.' &euro; ('.\round($per_amount).'&#37;) de aportes sobre minimo')
                            )));
                            $log->doAdmin('project');

                            // evento público
                            $log->populate($project->name, null,
                                Text::html('feed-project_fail',
                                    Feed::item('project', $project->name, $project->id),
                                    $project->amount,
                                    \round($per_amount)
                            ));
                            $log->doPublic('projects');

                            unset($log);

                            //Email de proyecto fallido al autor
                            self::toOwner('fail', $project);
                            //Email de proyecto fallido a los inversores
                            self::toInvestors('fail', $project);
                        }
                        
                        echo '<br />';
                    } else {
                        // tiene hasta 80 días para conseguir el óptimo (o más)
                        if ($days >= 80) {
                            if ($debug) echo 'Ha llegado a los 80 dias de campaña (final de segunda ronda)<br />';

                            echo $project->name . ': ha recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . '<br />';
                            echo 'Ha llegado a los 80 días: financiado. ';

                            $execute = true; // ejecutar los cargos de la segunda ronda

                            $errors = array();
                            if ($project->succeed($errors)) {
                                $log_text = 'El proyecto %s ha sido %s obteniendo %s';
                            } else {
                                @mail('goteo_fail@doukeshi.org',
                                    'Fallo al marcar financiado ' . SITE_URL,
                                    'Fallo al marcar el proyecto '.$project->name.' como financiado ' . implode(',', $errors));
                                echo 'ERROR::' . implode(',', $errors);
                                $log_text = 'El proyecto %s ha fallado al ser, %s obteniendo %s';
                            }

                            // Evento Feed y mails solo si ejecucion automatica
                            if (\defined('CRON_EXEC')) {
                                $log = new Feed();
                                $log->setTarget($project->id);
                                $log->populate('proyecto supera segunda ronda (cron)', '/admin/projects',
                                    \vsprintf($log_text, array(
                                        Feed::item('project', $project->name, $project->id),
                                        Feed::item('relevant', 'financiado'),
                                        Feed::item('money', $project->amount.' &euro; ('.\round($per_amount).'%) de aportes sobre minimo')
                                )));
                                $log->doAdmin('project');

                                // evento público
                                $log->populate($project->name, null, Text::html('feed-project_finish',
                                                Feed::item('project', $project->name, $project->id),
                                                $project->amount,
                                                \round($per_amount)
                                                ));
                                $log->doPublic('projects');
                                unset($log);

                                //Email de proyecto final segunda ronda al autor
                                self::toOwner('r2_pass', $project);
                                //Email de proyecto final segunda ronda a los inversores
                                self::toInvestors('r2_pass', $project);

                                // Tareas para gestionar
                                // calculamos fecha de passed+90 días
                                $passtime = strtotime($project->passed);
                                $limsec = date('d/m/Y', \mktime(0, 0, 0, date('m', $passtime), date('d', $passtime)+89, date('Y', $passtime)));

                                $task = new Model\Task();
                                $task->node = \GOTEO_NODE;
                                $task->text = "Hacer los pagos secundarios al proyecto <strong>{$project->name}</strong> antes del día <strong>{$limsec}</strong>";
                                $task->url = "/admin/accounts/?projects={$project->id}";
                                $task->done = null;
                                $task->save();

                                // y preparar contrato
                                $task = new Model\Task();
                                $task->node = \GOTEO_NODE;
                                $task->text = date('d/m/Y').": Enviar datos contrato <strong>{$project->name}</strong>, {$project->user->name}";
                                //@TODO enlace a gestión de contrato
                                $task->url = "/admin/projects?proj_name={$project->name}";
                                $task->done = null;
                                $task->save();
                                
                                // + mail a mercè
                                @mail(\GOTEO_CONTACT_MAIL,
                                    'Preparar contrato ' . $project->name,
                                    'El proyecto '.$project->name.' ha pasado la primera ronda, enviarle los datos de contrato. Se ha creado una tarea para esto.');
                            }

                            echo '<br />';
                        } elseif (empty($project->passed)) {

                            if ($debug) echo 'Ha llegado a los 40 dias de campaña, pasa a segunda ronda<br />';

                            echo $project->name . ': ha recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . '<br />';
                            echo 'El proyecto supera la primera ronda: marcamos fecha';

                            $execute = true; // ejecutar los cargos de la primera ronda

                            $errors = array();
                            if ($project->passed($errors)) {
                                echo ' -> Ok';
                            } else {
                                @mail('goteo_fail@doukeshi.org',
                                    'Fallo al marcar fecha de paso a segunda ronda ' . SITE_URL,
                                    'Fallo al marcar la fecha de paso a segunda ronda para el proyecto '.$project->name.': ' . implode(',', $errors));
                                echo ' -> ERROR::' . implode(',', $errors);
                            }

                            echo '<br />';

                            // Evento Feed solo si ejecucion automatica
                            if (\defined('CRON_EXEC')) {
                                $log = new Feed();
                                $log->setTarget($project->id);
                                $log->populate('proyecto supera primera ronda (cron)', '/admin/projects', \vsprintf('El proyecto %s %s en segunda ronda obteniendo %s', array(
                                    Feed::item('project', $project->name, $project->id),
                                    Feed::item('relevant', 'continua en campaña'),
                                    Feed::item('money', $project->amount.' &euro; ('.\number_format($per_amount, 2).'%) de aportes sobre minimo')
                                )));
                                $log->doAdmin('project');

                                // evento público
                                $log->populate($project->name, null,
                                    Text::html('feed-project_goon',
                                        Feed::item('project', $project->name, $project->id),
                                        $project->amount,
                                        \round($per_amount)
                                ));
                                $log->doPublic('projects');
                                unset($log);

                                if ($debug) echo 'Email al autor y a los cofinanciadores<br />';
                                // Email de proyecto pasa a segunda ronda al autor
                                self::toOwner('r1_pass', $project);

                                //Email de proyecto pasa a segunda ronda a los inversores
                                self::toInvestors('r1_pass', $project);
                                
                                // Tarea para hacer los pagos
                                $task = new Model\Task();
                                $task->node = \GOTEO_NODE;
                                $task->text = date('d/m/Y').": Pagar a <strong>{$project->name}</strong>, {$project->user->name}";
                                $task->url = "/admin/projects/report/{$project->id}";
                                $task->done = null;
                                $task->save();
                                
                                // + mail a susana
                                @mail('susana@goteo.org',
                                    'Pagar al proyecto ' . $project->name,
                                    'El proyecto '.$project->name.' ha terminado la segunda ronda, hacer los pagos. Se ha creado una tarea para esto.');
                            }
                            
                        } else {
                            if ($debug) echo 'Lleva más de 40 dias de campaña, debe estar en segunda ronda con fecha marcada<br />';
                            if ($debug) echo $project->name . ': lleva recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . ' y paso a segunda ronda el '.$project->passed.'<br />';
                        }
                    }
                }

                // si hay que ejecutar o cancelar
                if ($cancelAll || $execute) {
                    if ($debug) echo '::::::Comienza tratamiento de aportes:::::::<br />';
                    if ($debug) echo 'Execute=' . (string) $execute . '  CancelAll=' . (string) $cancelAll . '<br />';
                    // tratamiento de aportes penddientes
                    $query = \Goteo\Core\Model::query("
                        SELECT  *
                        FROM  invest
                        WHERE   invest.project = ?
                        AND     (invest.status = 0
                            OR (invest.method = 'tpv'
                                AND invest.status = 1
                            )
                            OR (invest.method = 'cash'
                                AND invest.status = 1
                            )
                        )
                        AND (invest.campaign IS NULL OR invest.campaign = 0)
                        ", array($project->id));
                    $project->invests = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');

                    foreach ($project->invests as $key=>$invest) {
                        $errors = array();
                        $log_text = null;
                        
                        $userData = Model\User::getMini($invest->user);

                        if ($invest->invested == date('Y-m-d')) {
                            if ($debug) echo 'Aporte ' . $invest->id . ' es de hoy.<br />';
                        } elseif ($invest->method != 'cash' && empty($invest->preapproval)) {
                            //si no tiene preaproval, cancelar
                            echo 'Aporte ' . $invest->id . ' cancelado por no tener preapproval.<br />';
                            $invest->cancel();
                            Model\Invest::setDetail($invest->id, 'no-preapproval', 'Aporte cancelado porque no tiene preapproval. Proceso cron/execute');
                            continue;
                        }

                        if ($cancelAll) {
                            if ($debug) echo 'Cancelar todo<br />';

                            switch ($invest->method) {
                                case 'paypal':
                                    $err = array();
                                    if (Paypal::cancelPreapproval($invest, $err, true)) {
                                        $log_text = "Se ha cancelado aporte y preapproval de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                                    } else {
                                        $txt_errors = implode('; ', $err);
                                        $log_text = "Ha fallado al cancelar el aporte de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                                    }
                                    break;
                                case 'tpv':
                                    // se habre la operación en optra ventana
                                    $err = array();
                                    if (Tpv::cancelPreapproval($invest, $err, true)) {
                                        $log_text = "Se ha anulado el cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s";
                                    } else {
                                        $txt_errors = implode('; ', $err);
                                        $log_text = "Ha fallado al anular el cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                                    }
                                    break;
                                case 'cash':
                                    if ($invest->cancel(true)) {
                                        $log_text = "Se ha cancelado aporte manual de %s de %s (id: %s) al proyecto %s del dia %s";
                                    } else{
                                        $log_text = "Ha fallado al cancelar el aporte manual de %s de %s (id: %s) al proyecto %s del dia %s. ";
                                    }
                                    break;
                        }

                            // Evento Feed admin
                            $log = new Feed();
                            $log->setTarget($project->id);
                            $log->populate('Preapproval cancelado por proyecto archivado (cron)', '/admin/invests', \vsprintf($log_text, array(
                                Feed::item('user', $userData->name, $userData->id),
                                Feed::item('money', $invest->amount.' &euro;'),
                                Feed::item('system', $invest->id),
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                            )));
                            $log->doAdmin();
                            unset($log);

                            echo 'Aporte '.$invest->id.' cancelado por proyecto caducado.<br />';
                            $invest->setStatus('4');
                            Model\Invest::setDetail($invest->id, 'project-expired', 'Aporte marcado como caducado porque el proyecto no ha tenido exito. Proceso cron/execute');

                            continue;
                        }

                        // si hay que ejecutar
                        if ($execute && empty($invest->payment)) {
                            if ($debug) echo 'Ejecutando aporte '.$invest->id.' ['.$invest->method.']';

                            switch ($invest->method) {
                                case 'paypal':
                                    if (empty($projectAccount->paypal)) {
                                        if ($debug) echo '<br />El proyecto '.$project->name.' no tiene cuenta paypal.<br />';
                                        Model\Invest::setDetail($invest->id, 'no-paypal-account', 'El proyecto no tiene cuenta paypal en el momento de ejecutar el preapproval. Proceso cron/execute');
                                        break;
                                    }

                                    $invest->account = $projectAccount->paypal;
                                    $err = array();
                                    if (Paypal::pay($invest, $err)) {
                                        $log_text = "Se ha ejecutado el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                                        if ($debug) echo ' -> Ok';
                                        Model\Invest::setDetail($invest->id, 'executed', 'Se ha ejecutado el preapproval, ha iniciado el pago encadenado. Proceso cron/execute');
                                        // si era incidencia la desmarcamos
                                        if ($invest->issue) {
                                            Model\Invest::unsetIssue($invest->id);
                                            Model\Invest::setDetail($invest->id, 'issue-solved', 'La incidencia se ha dado por resuelta al ejecutarse correctamente en el proceso automático');
                                        }
                                    } else {
                                        $txt_errors = implode('; ', $err);
                                        echo 'Aporte ' . $invest->id . ': Fallo al ejecutar cargo paypal: ' . $txt_errors . '<br />';
                                        @mail('goteo_fail@doukeshi.org',
                                            'Fallo al ejecutar cargo Paypal ' . SITE_URL,
                                            'Aporte ' . $invest->id . ': Fallo al ejecutar cargo paypal: ' . $txt_errors);
                                        if ($debug) echo ' -> ERROR!!';
                                        Model\Invest::setDetail($invest->id, 'execution-failed', 'Fallo al ejecutar el preapproval, no ha iniciado el pago encadenado: ' . $txt_errors . '. Proceso cron/execute');

                                        // Notifiacion de incidencia al usuario
                                        // Obtenemos la plantilla para asunto y contenido
                                        $template = Template::get(37);
                                        // Sustituimos los datos
                                        $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                                        $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%DETAILS%');
                                        $replace = array($userData->name, $project->name, SITE_URL . '/project/' . $project->id, $invest->amount, '');
                                        $content = \str_replace($search, $replace, $template->text);
                                        // iniciamos mail
                                        $mailHandler = new Mail();
                                        $mailHandler->from = GOTEO_CONTACT_MAIL;
                                        $mailHandler->to = $userData->email;
                                        $mailHandler->toName = $userData->name;
                                        $mailHandler->subject = $subject;
                                        $mailHandler->content = $content;
                                        $mailHandler->html = true;
                                        $mailHandler->template = $template->id;
                                        if ($mailHandler->send()) {
                                            Model\Invest::setDetail($invest->id, 'issue-notified', "Se ha notificado la incidencia al usuario");
                                        } else {
                                            Model\Invest::setDetail($invest->id, 'issue-notify-failed', "Ha fallado al enviar el mail de notificacion de la incidencia al usuario");
                                            @mail('goteo_fail@doukeshi.org',
                                                'Fallo al enviar email de notificacion de incidencia PayPal' . SITE_URL,
                                                'Fallo al enviar email de notificacion de incidencia PayPal: <pre>' . print_r($mailHandler, 1). '</pre>');
                                        }
                                        
                                    }
                                    break;
                                case 'tpv':
                                    // los cargos con este tpv vienen ejecutados de base
                                    if ($debug) echo ' -> Ok';
                                /*
                                    $err = array();
                                    if (Tpv::pay($invest, $err)) {
                                        echo 'Cargo sermepa correcto';
                                        $log_text = "Se ha ejecutado el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s";
                                    } else {
                                        $txt_errors = implode('; ', $err);
                                        echo 'Fallo al ejecutar cargo sermepa: ' . $txt_errors;
                                        $log_text = "Ha fallado al ejecutar el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s <br />Se han dado los siguientes errores: $txt_errors";
                                    }
                                 *
                                 */
                                    break;
                                case 'cash':
                                    // los cargos manuales vienen ejecutados de base
                                    $invest->setStatus('1');
                                    if ($debug) echo ' -> Ok';
                                    break;
                            }
                            if ($debug) echo '<br />';

                            if (!empty($log_text)) {
                                // Evento Feed
                                $log = new Feed();
                                $log->setTarget($project->id);
                                $log->populate('Cargo ejecutado (cron)', '/admin/invests', \vsprintf($log_text, array(
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('money', $invest->amount.' &euro;'),
                                    Feed::item('system', $invest->id),
                                    Feed::item('project', $project->name, $project->id),
                                    Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                                )));
                                $log->doAdmin();
                                if ($debug) echo $log->html . '<br />';
                                unset($log);
                            }

                            if ($debug) echo 'Aporte '.$invest->id.' tratado<br />';
                        }

                    }

                    if ($debug) echo '::Fin tratamiento aportes<br />';
                }

                if ($debug) echo 'Fin tratamiento Proyecto '.$project->name.'<hr />';
            }


            // checkeamos campañas activas
            $campaigns = Model\Call::getActive(4);
            foreach ($campaigns as $campaign) {
                $errors = array();

                // tiene que tener presupuesto
                if (empty($campaign->amount)) {
                    continue;
                }

                // si le quedan cero
                // -> terminar la campaña exitosamente
                if ($campaign->rest == 0 && !empty($campaign->amount))  {
                    echo 'La convocatoria '.$campaign->name.': ';
                    if ($campaign->checkSuccess($errors)) {
                        if ($campaign->succeed($errors)) {
                            echo 'Ha terminado exitosamente.<br />';

                            $log = new Feed();
                            $log->setTarget($campaign->id, 'call');
                            $log->unique = true;
                            $log->populate('Campaña terminada (cron)', '/admin/calls/'.$campaign->id.'?rest='.$amount,
                                \vsprintf('La campaña %s ha terminado con exito', array(
                                    Feed::item('call', $campaign->name, $campaign->id))
                                ));
                            $log->doAdmin('call');
                            $log->populate($campaign->name, '/call/'.$campaign->id.'?rest='.$amount,
                                \vsprintf('La campaña %s ha terminado con éxito', array(
                                    Feed::item('call', $campaign->name, $campaign->id))
                                ), $call->logo);
                            $log->doPublic('projects');
                            unset($log);

                        } else {
                            echo 'Ha fallado al marcar exitosa.<br />'.implode('<br />', $errors);
                        }
                    } else {
                        echo 'Le Queda algun proyecto en primera ronda.<br />';
                    }
                }

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
            $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0777);
        }


        /*
         *  Proceso que verifica si los preapprovals han sido coancelados
         *   Solamente trata transacciones paypal pendientes de proyectos en campaña
         *
         */
        public function verify () {
            if (!\defined('CRON_EXEC')) {
                @mail('goteo_cron@doukeshi.org', 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
               echo 'Lanzamiento manual<br />';
            } else {
               echo 'Lanzamiento automatico<br />';
            }
            // eliminamos feed antiguo
            $sql = "DELETE 
                FROM `feed` 
                WHERE type != 'goteo' 
                AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`datetime`)), '%j') > 50
                AND (url NOT LIKE '%updates%' OR url IS NULL)
                ";
            
            // echo $sql . '<br />';
            $query = Model\Project::query($sql);
            $count = $query->rowCount();
            echo "Eliminados $count registros de feed.<br />";
            
            // eliminamos mail antiguo
            $sql2 = "DELETE
                FROM `mail` 
                WHERE (template != 33 OR template IS NULL)
                AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`date`)), '%j') > 60
                ";
            
            // echo $sql2 . '<br />';
            $query2 = Model\Project::query($sql2);
            $count2 = $query2->rowCount();
            echo "Eliminados $count2 registros de mail.<br />";
            
            // eliminamos registros de imágenes cuyo archivo no esté en el directorio de imágenes
            
            
            // eliminamos aportes incompletos
            $sql4 = "DELETE
                FROM `invest` 
                WHERE status = -1
                AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`datetime`)), '%j') > 30
                ";
            
            //echo $sql4 . '<br />';
            $query4 = Model\Project::query($sql4);
            $count4 = $query4->rowCount();
            // -- eliminamos registros relativos a aportes no existentes
            Model\Project::query("DELETE FROM `invest_address` WHERE invest NOT IN (SELECT id FROM `invest`)");
            Model\Project::query("DELETE FROM `invest_detail`  WHERE invest NOT IN (SELECT id FROM `invest`)");
            Model\Project::query("DELETE FROM `invest_reward`  WHERE invest NOT IN (SELECT id FROM `invest`)");
            echo "Eliminados $count4 aportes incompletos y sus registros (recompensa, dirección, detalles) relacionados.<br />";
            
            
            echo "<hr /> Iniciamos caducidad de tokens<br/>";
            // eliminamos los tokens que tengan más de 4 días
            $sql5 = "SELECT id, token FROM user WHERE token IS NOT NULL AND token != '' AND token LIKE '%¬%'";
            $query5 = Model\Project::query($sql5);
            foreach ($query5->fetchAll(\PDO::FETCH_OBJ) as $row) {
                $parts = explode('¬', $row->token);
                $datepart = strtotime($parts[2]);
                $today = date('Y-m-d');
                $datedif = strtotime($today) - $datepart;
                $days = round($datedif / 86400);
                if ($days > 4 || !isset($parts[2])) {
                    echo "User: $row->id  ;  Token: $row->token  ; ";
                    echo "Datepart: $parts[2]   =>  $datepart  ; ";
                    echo "Compare: $today  =>  $datedif  ;  ";
                    echo "Days: $days  ;   ";
                    
                    if (Model\Project::query("UPDATE user SET token = '' WHERE id = ?", array($row->id))) {
                        echo "Token borrado.";
                    } else {
                        echo "Fallo al borrar Token!!!";
                    }
                    echo "<br />";
                }
                
            }
            
            echo "<br />";
                
            echo 'Listo!';
            // recogemos el buffer para grabar el log
            $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0777);
            
            die();

            /*
            // proyectos en campaña
            $projects = Model\Project::active(true);

            foreach ($projects as &$project) {
                // aportes de ese proyecto que esten pendientes de cargo
//                $timeago = date('Y-m-d', \mktime(0, 0, 0, date('m'), date('d')-30, date('Y')));
                $query = Model\Project::query("
                    SELECT  *
                    FROM  invest
                    WHERE   invest.status = 0
                    AND     invest.method = 'paypal'
                    AND     invest.project = ?
                    ", array($project->id));
//                    AND     invest.invested <= '{$timeago}'
                $project->invests = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');

                if (empty($project->invests)) continue;

//                echo "Proyecto: {$project->name} <br />Aportes pendientes: " . count($project->invests) . "<br />";

                foreach ($project->invests as $key=>&$invest) {

                    $details = null;
                    $errors = array();

                    if (empty($invest->preapproval)) {
                        // no tiene preaproval, cancelar
                        echo 'Aporte ' . $invest->id . ' del ' . $invest->invested . ' No tiene preapproval, aporte cancelado<br />';
                        $invest->cancel();
                        Model\Invest::setDetail($invest->id, 'no-preapproval', 'Aporte cancelado en el proceso cron/verify porque no tiene preapproval');
                    } else {
                        // comprobar si está cancelado por el usuario
                        if ($details = Paypal::preapprovalDetails($invest->preapproval, $errors)) {

                            // actualizar la cuenta de paypal que se validó para aprobar
                            $invest->setAccount($details->senderEmail);

                            // si está aprobado y el aporte está en proceso, lo marcamos como pendiente de cargo
                            if ($details->approved == true && $invest->status == '-1') {
                                $invest->setStatus('0');
                                Model\Invest::setDetail($invest->id, 'set-status-0', 'El Aporte estaba \'En proceso\' pero los detalles dicen que el preapproval está aprobado. Cambio estado a \'pendiente de cargo\' en el proceso cron/verify');
                            }

//                            echo \trace($details);
                            switch ($details->status) {
                                case 'ACTIVE':
                                    //echo 'Sigue activo<br />';
                                    break;
                                case 'CANCELED':
                                    echo 'Proyecto: '.$project->name.' <br /> Aporte ' . $invest->id . ' del ' . $invest->invested . '  Preapproval cancelado por el usuario<br />';
                                    $invest->cancel();
                                    Model\Invest::setDetail($invest->id, 'preapproval_canceled', 'Preapproval cancelado por el usuario, aporte cancelado en el proceso cron/verify');
                                    @mail('goteo_fail@doukeshi.org',
                                        'Preapproval cancelado por el usuario ' . SITE_URL,
                                        'Aporte ' . $invest->id . ': al pedir detalles paypal: Cancelado por el usuario');
                                    break;
                                case 'DEACTIVED':
                                    echo 'Proyecto: '.$project->name.' <br /> Aporte ' . $invest->id . ' del ' . $invest->invested . '  Preapproval Desactivado!<br />';
                                    $invest->cancel();
                                    Model\Invest::setDetail($invest->id, 'preapproval_canceled', 'Preapproval está desactivado, aporte cancelado en el proceso cron/verify');
                                    @mail('goteo_fail@doukeshi.org',
                                        'Preapproval desactivado ' . SITE_URL,
                                        'Aporte ' . $invest->id . ': al pedir detalles paypal: Está desactivado!!');
                                    break;
                            }
                        } else {
                            @mail('goteo_fail@doukeshi.org',
                                'errores al pedir detalles Paypal ' . SITE_URL,
                                'Aporte ' . $invest->id . ': al pedir detalles paypal: Errores:<br />' . implode('<br />', $errors));
                        }
                    }
                }
            }


            */
        }

        /*
         * Realiza los pagos secundarios al proyecto
         *
         * Esto son los aportes de tipo paypal, ejecutados (status 1), que tengan payment code
         *
         */
        public function dopay ($project) {
            if (\defined('CRON_EXEC')) {
                die('Este proceso no necesitamos lanzarlo automaticamente');
            }

            @mail('goteo_cron@doukeshi.org', 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                'Se ha lanzado manualmente el cron '. __FUNCTION__ .' para el proyecto '.$project.' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
            
            // a ver si existe el bloqueo
            $block_file = GOTEO_PATH.'logs/cron-'.__FUNCTION__.'.block';
            if (file_exists($block_file)) {
                echo 'Ya existe un archivo de log '.date('Ymd').'_'.__FUNCTION__.'.log<br />';
                $block_content = \file_get_contents($block_file);
                echo 'El contenido del bloqueo es: '.$block_content;
                // lo escribimos en el log
                $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
                \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
                \chmod($log_file, 0777);
                @mail('goteo_cron@doukeshi.org', 'Cron '. __FUNCTION__ .' bloqueado en ' . SITE_URL,
                    'Se ha encontrado con que el cron '. __FUNCTION__ .' está bloqueado el '.date('d-m-Y').' a las ' . date ('H:i:s') . '
                        El contenido del bloqueo es: '. $block_content);
                die;
            } else {
                $block = 'Bloqueo '.$block_file.' activado el '.date('d-m-Y').' a las '.date ('H:i:s').'<br />';
                if (\file_put_contents($block_file, $block, FILE_APPEND)) {
                    \chmod($block_file, 0777);
                    echo $block;
                } else {
                    echo 'No se ha podido crear el archivo de bloqueo<br />';
                    @mail('goteo_cron@doukeshi.org', 'Cron '. __FUNCTION__ .' no se ha podido bloquear en ' . SITE_URL,
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
            $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0777);
        }

        /**
         * Al autor del proyecto
         *
         * @param $type string
         * @param $project Object
         * @return bool
         */
        static private function toOwner ($type, $project) {
            $tpl = null;
            /// tipo de envio
            switch ($type) {
                case '8_days': // template 13, cuando faltan 8 días y no ha conseguido el mínimo
                    $tpl = 13;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/widgets');
                    break;

                case '1_day': // template 14, cuando falta un día, no minimo pero si 70%
                    $tpl = 14;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/widgets');
                    break;

                case '20_days': // template 19, 20 días de campaña
                    $tpl = 19;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/widgets');
                    break;

                case 'r1_pass': // template 20, proyecto supera la primera ronda
                    $tpl = 20;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%WIDGETURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/widgets');
                    break;

                case 'fail': // template 21, caduca sin conseguir el mínimo
                    $tpl = 21;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%SUMMARYURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/summary');
                    break;

                case 'r2_pass': // template 22, finaliza segunda ronda
                    $tpl = 22;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%REWARDSURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/rewards');
                    break;

                case 'no_updates': // template 23, 3 meses sin novedades
                    $tpl = 23;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%UPDATESURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/updates');
                    break;

                case 'no_activity': // template 24, 3 meses sin actividad (no mensajes ni comentarios)
                    $tpl = 24;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%UPDATESURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/updates');
                    break;

                case '2m_after': // template 25, dos meses despues de financiado
                    $tpl = 25;
                    $search  = array('%USERNAME%', '%PROJECTNAME%', '%REWARDSURL%');
                    $replace = array($project->user->name, $project->name, SITE_URL . '/dashboard/projects/rewards');
                    break;
            }

            if (!empty($tpl)) {
                // Obtenemos la plantilla para asunto y contenido
                $template = Template::get($tpl);
                // Sustituimos los datos
                $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                $content = \str_replace($search, $replace, $template->text);
                // iniciamos mail
                $mailHandler = new Mail();
                $mailHandler->to = $project->user->email;
                $mailHandler->toName = $project->user->name;
                // blind copy a goteo desactivado durante las verificaciones
    //              $mailHandler->bcc = 'comunicaciones@goteo.org';
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;
                $mailHandler->html = true;
                $mailHandler->template = $template->id;
                if ($mailHandler->send()) {
                    return true;
                } else {
                    @mail('goteo_fail@doukeshi.org',
                        'Fallo al enviar email automaticamente al autor ' . SITE_URL,
                        'Fallo al enviar email automaticamente al autor: <pre>' . print_r($mailHandler, 1). '</pre>');
                }
            }

            return false;
        }

        /* A los cofinanciadores */
        static public function toInvestors ($type, $project) {

            // notificación
            $notif = $type == 'update' ? 'updates' : 'rounds';

            $anyfail = false;
            $tpl = null;

            // para cada inversor que no tenga bloqueado esta notificacion
            $sql = "
                SELECT
                    invest.user as id,
                    user.name as name,
                    user.email as email,
                    invest.method as method,
                    IFNULL(user.lang, 'es') as lang
                FROM  invest
                INNER JOIN user
                    ON user.id = invest.user
                    AND user.active = 1
                LEFT JOIN user_prefer
                    ON user_prefer.user = invest.user
                WHERE   invest.project = ?
                AND invest.status IN ('0', '1', '3', '4')
                AND (user_prefer.{$notif} = 0 OR user_prefer.{$notif} IS NULL)
                GROUP BY user.id
                ";
            if ($query = \Goteo\Core\Model::query($sql, array($project->id))) {
                foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $investor) {
                    /// tipo de envio
                    switch ($type) {
                        case 'r1_pass': // template 15, proyecto supera la primera ronda
                                $tpl = 15;
                                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%');
                                $replace = array($investor->name, $project->name, SITE_URL . '/project/' . $project->id);
                            break;

                        case 'fail': // template 17 (paypalistas) / 35 (tpvistas) , caduca sin conseguir el mínimo
                                $tpl = ($investor->method == 'paypal') ? 17 : 35;
                                $search  = array('%USERNAME%', '%PROJECTNAME%', '%DISCOVERURL%');
                                $replace = array($investor->name, $project->name, SITE_URL . '/discover');
                            break;

                        case 'r2_pass': // template 16, finaliza segunda ronda
                                $tpl = 16;
                                $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%');
                                $replace = array($investor->name, $project->name, SITE_URL . '/project/' . $project->id);
                            break;

                        case 'update': // template 18, publica novedad
                                $tpl = 18;
                                $search  = array('%USERNAME%', '%PROJECTNAME%', '%UPDATEURL%');
                                $replace = array($investor->name, $project->name, SITE_URL.'/project/'.$project->id.'/updates');
                            break;
                    }

                    if (!empty($tpl)) {
                        // Obtenemos la plantilla para asunto y contenido
                        // en el idioma del usuario
                        $template = Template::get($tpl, $investor->lang);
                        // Sustituimos los datos
                        $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                        $content = \str_replace($search, $replace, $template->text);
                        // iniciamos mail
                        $mailHandler = new Mail();
                        $mailHandler->to = $investor->email;
                        $mailHandler->toName = $investor->name;
                        // blind copy a goteo desactivado durante las verificaciones
            //              $mailHandler->bcc = 'comunicaciones@goteo.org';
                        $mailHandler->subject = $subject;
                        $mailHandler->content = $content;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        if ($mailHandler->send()) {

                        } else {
                            $anyfail = true;
                            @mail('goteo_fail@doukeshi.org',
                                'Fallo al enviar email automaticamente al cofinanciador ' . SITE_URL,
                                'Fallo al enviar email automaticamente al cofinanciador: <pre>' . print_r($mailHandler, 1). '</pre>');
                        }
                        unset($mailHandler);
                    }
                }
                // fin bucle inversores
            } else {
                echo '<p>'.str_replace('?', $project->id, $sql).'</p>';
                $anyfail = true;
            }
            
            if ($anyfail)
                return false;
            else
                return true;

        }


        /**
         *  Proceso para enviar avisos a los autores segun
         *  - Que lleven 3 meses sin publicar actualizaciones, envia cada mes
         *  - Que lleven 3 meses sin decir nada (?), envia cada 15 dias
         *  - Que hayan pasado dos meses desde que se dio por financiado, cada 15 dias
         *
         *  teiene en cuenta que se envía cada tantos días
         */
        
        public function daily () {
            if (!\defined('CRON_EXEC')) {
                @mail('goteo_cron@doukeshi.org', 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
                die('Este proceso no necesitamos lanzarlo manualmente');
            }
            
            // proyectos en campaña o financiados
            $projects = Model\Project::active();

            // para cada uno,
            foreach ($projects as $project) {

                // dias desde la publicacion
                $from = $project->daysActive();

                // pero seguimos trabajando con el numero de dias que lleva para enviar mail al autor
                // cuando quedan 20 días
                if ($project->round == 1 && $project->days == 20) {
                    self::toOwner('20_days', $project);
                    echo 'Enviado Aviso al autor  del proyecto ' . $project->name . ', lleva 20 dias de campaña<br />';
                }
                // cuando quedan 8 dias y no ha conseguido el minimo
                if ($project->round == 1 && $project->days == 8
                    && $project->amount < $project->mincost) {
                    self::toOwner('8_days', $project);
                    echo 'Enviado Aviso al autor del Proyecto: ' . $project->name . ', le faltan 8 dias y no ha conseguido el minimo<br />';
                }
                // cuando queda 1 día y no ha conseguido el minimo pero casi
                if ($project->round == 1 && $project->days == 1
                    && $project->amount < $project->mincost && \round(($project->amount / $project->mincost) * 100) > 70) {
                    self::toOwner('1_day', $project);
                    echo 'Enviado Aviso al autor del proyecto ' . $project->name . ', falta 1 dia y no supera el 70 el minimo<br />';
                }
                /* Fin verificacion */



                // si ya lleva 3 meses de publicacion, hasta máximo un año
                if ($from > 90 && $from < 360) {
                    //   mirar el tiempo desde la última actualización,
                    $sql = "
                        SELECT
                            DATE_FORMAT(
                                from_unixtime(unix_timestamp(now()) - unix_timestamp(date))
                                , '%j'
                            ) as days
                        FROM post
                        INNER JOIN blog
                            ON  post.blog = blog.id
                            AND blog.type = 'project'
                            AND blog.owner = :project
                        WHERE post.publish = 1
                        ORDER BY post.date DESC
                        LIMIT 1";
    //                echo str_replace(':project', "'{$project->id}'", $sql) . '<br />';
                    $query = Model\Project::query($sql, array(':project' => $project->id));
                    $lastupdate = $query->fetchColumn(0);
                    // si hace más de 3 meses, o nunca a posteado
                    if ((int) $lastupdate > 90 || $lastupdate === false) {
                        // mirar el ultimo mensaje al email del autor con la plantilla 23
                        $sql = "
                            SELECT
                                DATE_FORMAT(
                                    from_unixtime(unix_timestamp(now()) - unix_timestamp(date))
                                    , '%j'
                                ) as days
                            FROM mail
                            WHERE mail.email = :email
                            AND mail.template = 23
                            ORDER BY mail.date DESC
                            LIMIT 1";
    //                    echo str_replace(':email', "'{$project->user->email}'", $sql) . '<br />';
                        $query = Model\Project::query($sql, array(':email' => $project->user->email));
                        $lastsend = $query->fetchColumn(0);
                        // si hace más de un mes o nunca se le envió
                        if ($lastsend > 30 || $lastsend === false) {
                            // enviar email no_updates
                            self::toOwner('no_updates', $project);
                            echo 'Enviado Aviso (sin novedades: plantilla 23) al autor del proyecto ' . $project->name . ', la última novedad es de hace ' . $lastupdate . ' dias, el último aviso se le envió hace ' . $lastsend . ' dias<br />';
                        }
                    }
                }

                /*
                 * Ya no enviamos más el aviso de sin actuividad por mensajes/comentarios
                 * 
                // si ya lleva 3 meses de publicacion
                if ($from > 90) {
                    if ($project->id == 'tuderechoasaber.es') break;
                    
                    // mirar el tiempo desde su último mensaje o comentario en su proyecto
                    $sql = "
                        SELECT
                            IF (comment.date < message.date,
                                DATE_FORMAT(
                                    from_unixtime(unix_timestamp(now()) - unix_timestamp(comment.date))
                                    , '%j'
                                ),
                                DATE_FORMAT(
                                    from_unixtime(unix_timestamp(now()) - unix_timestamp(message.date))
                                    , '%j'
                                )
                            ) as days
                        FROM message
						LEFT JOIN `comment` 
							ON comment.user = :owner
							AND comment.post IN (
                            SELECT post.id
                            FROM post
                            INNER JOIN blog
                                ON  post.blog = blog.id
                                AND blog.type = 'project'
                                AND blog.owner = :project
                            WHERE post.publish = 1
							) 
                        WHERE message.project = :project
							AND message.user = :owner
                        ORDER BY `days` ASC
                        LIMIT 1";
    //                echo str_replace(array(':project', ':owner'), array("'{$project->id}'", "'{$project->owner}'"), $sql) . '<br />';
                    $query = Model\Project::query($sql, array(':project' => $project->id, ':owner' => $project->owner));
                    $lastactivity = $query->fetchColumn(0);
                    // si hace más de 3 meses, o nunca ha dicho nada
                    if ((int) $lastactivity > 90 || ($lastactivity === false && $from > 90)) {
                        // mirar el ultimo mensaje al email del autor con la plantilla 24
                        $sql = "
                            SELECT
                                DATE_FORMAT(
                                    from_unixtime(unix_timestamp(now()) - unix_timestamp(date))
                                    , '%j'
                                ) as days
                            FROM mail
                            WHERE mail.email = :email
                            AND mail.template = 24
                            ORDER BY mail.date DESC
                            LIMIT 1";
    //                    echo str_replace(':email', "'{$project->user->email}'", $sql) . '<br />';
                        $query = Model\Project::query($sql, array(':email' => $project->user->email));
                        $lastsend = $query->fetchColumn(0);
                        // si hace más de 15 días o nunca se le envió
                        if ($lastsend > 15 || $lastsend === false) {
                            // enviar email no_activity
                            self::toOwner('no_activity', $project);
                            echo 'Enviado Aviso (sin mensajes ni comentarios: plantilla 24) al autor del proyecto ' . $project->name . ', la última actividad es de hace ' . $lastactivity . ' dias, el último aviso se le envió hace ' . $lastsend . ' dias<br />';
                        }
                    }
                }
                */
                
                // para los financiados
                if ($project->status == 4) {
                    // mirar el tiempo desde la fecha success
                    $sql = "
                        SELECT
                            DATE_FORMAT(
                                from_unixtime(unix_timestamp(now()) - unix_timestamp(success))
                                , '%j'
                            ) as days
                        FROM project
                        WHERE id = :project
                        AND success != '0000-00-00'
                        ";
    //                echo str_replace(':project', "'{$project->id}'", $sql) . '<br />';
                    $query = Model\Project::query($sql, array(':project' => $project->id));
                    // si esta financiado, claro
                    if ($lastsuccess = $query->fetchColumn(0)) {
                        // si hace más de 2 meses
                        if ((int) $lastsuccess > 60) {

                            // mirar si tiene todo cumplido (recompensas y retornos)
                            if (Model\Project\Reward::areFulfilled($project->id)
                                && Model\Project\Reward::areFulfilled($project->id, 'social') ) {
                                $msg = 'Tiene todo cumplido recompensas/retornos<br />';
                                continue;
                            } else {
                                $msg = 'Le quedan recompensas/retornos pendientes<br />';
                            }

                            // mirar el ultimo mensaje al email del autor con la plantilla 25
                            $sql = "
                                SELECT
                                    DATE_FORMAT(
                                        from_unixtime(unix_timestamp(now()) - unix_timestamp(date))
                                        , '%j'
                                    ) as days
                                FROM mail
                                WHERE mail.email = :email
                                AND mail.template = 25
                                ORDER BY mail.date DESC
                                LIMIT 1";
    //                        echo str_replace(':email', "'{$project->user->email}'", $sql) . '<br />';
                            $query = Model\Project::query($sql, array(':email' => $project->user->email));
                            $lastsend = $query->fetchColumn(0);
                            // si hace más de 15 días o nunca se le envió
                            if ($lastsend > 30 || $lastsend === false) {
                                // enviar email 2m_after
                                self::toOwner('2m_after', $project);
                                echo 'Enviado Aviso (recompensas pendientes: plantilla 25) al autor del proyecto ' . $project->name . ', '.$msg.', financiado hace ' . $lastsuccess . ' dias, el último aviso se le envió hace ' . $lastsend . ' dias<br />';
                            }
                        }
                    }
                }

            }

            // convocatorias con aplicación abierta
            $calls = Model\Call::getActive(3);
            foreach ($calls as $call) {
                // a ver cuantos días le quedan para que acabe la convocatoria
                $open = strtotime($call->opened);
                $until = mktime(0, 0, 0, date('m', $open), date('d', $open)+$call->days, date('Y', $open));
                $now = strtotime(date('Y-m-d'));
                $diference = $until - $now;
                $days = \round($diference/24/60/60);

                $doFeed = false;
                switch ($days) {
                    case 7:
                        $log_text = 'Falta una semana para que acabe la convocatoria %s';
                        $log_text_public = 'Falta una semana para que se cierre la aplicación de proyectos';
                        $doFeed = true;
                        break;
                    case 3:
                        $log_text = 'Faltan 3 dias para que acabe la convocatoria %s';
                        $log_text_public = 'Faltan 3 dias para que se cierre la aplicación de proyectos';
                        $doFeed = true;
                        break;
                    case 1:
                        $log_text = 'Ultimo día para la convocatoria %s';
                        $log_text_public = 'Hoy es el último día para aplicar proyectos!';
                        $doFeed = true;
                        break;
                }

                // feed
                if ($doFeed) {
                    $log = new Feed();
                    $log->setTarget($call->id, 'call');
                    $log->unique = true;
                    $log->populate('Convocatoria terminando (cron)', '/admin/calls/'.$call->id.'?days='.$days,
                        \vsprintf($log_text, array(
                            Feed::item('call', $call->name, $call->id))
                        ));
                    $log->doAdmin('call');
                    $log->populate('Convocatoria: ' . $call->name, '/call/'.$call->id.'?days='.$days, $log_text_public, $call->logo);
                    $log->doPublic('projects');
                    unset($log);
                    echo \vsprintf($log_text, array($call->name)).'<br />';
                }




            }



            // campañas dando dinero
            $campaigns = Model\Call::getActive(4);
            foreach ($campaigns as $campaign) {
                $errors = array();

                // tiene que tener presupuesto
                if (empty($campaign->amount)) {
                    continue;
                }

                // a ver cuanto le queda de capital riego
                $rest = $campaign->rest;

                $doFeed = false;
                if ($rest < 100) {
                    $amount = 100;
                    $doFeed = true;
                } elseif ($rest < 500) {
                    $amount = 500;
                    $doFeed = true;
                } elseif ($rest < 1000) {
                    $amount = 1000;
                    $doFeed = true;
                }
                // feed
                if ($doFeed) {
                    $log = new Feed();
                    $log->setTarget($campaign->id, 'call');
                    $log->unique = true;
                    $log->populate('Campaña terminando (cron)', '/admin/calls/'.$campaign->id.'?rest='.$amount,
                        \vsprintf('Quedan menos de %s en la campaña %s', array(
                            Feed::item('money', $amount.' &euro;')
                                . ' de '
                                . Feed::item('drop', 'Capital Riego', '/service/resources'),
                            Feed::item('call', $campaign->name, $campaign->id))
                        ));
                    $log->doAdmin('call');
                    $log->populate($campaign->name, '/call/'.$campaign->id.'?rest='.$amount,
                        \vsprintf('Quedan menos de %s en la campaña %s', array(
                            Feed::item('money', $amount.' &euro;') 
                                . ' de '
                                . Feed::item('drop', 'Capital Riego', '/service/resources'),
                            Feed::item('call', $campaign->name, $campaign->id))
                        ), $call->logo);
                    $log->doPublic('projects');
                    unset($log);
                }
            }


            // recogemos el buffer para grabar el log
            $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0777);
        }

    }
    
}
