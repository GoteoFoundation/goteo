<?php

namespace Goteo\Controller\Cron {

    use Goteo\Core\Exception;
    use Goteo\Model,
        Goteo\Library\Text,
        Goteo\Library\Feed,
        Goteo\Library\Template,
        Goteo\Library\Tpv,
        Goteo\Library\Paypal,
        Goteo\Library\Mail;

    class Execute {

        public static function process () {

            $debug = true;

            try {

                // revision de proyectos: dias, conseguido y cambios de estado
                // proyectos en campaña que estén a 5 días de terminar primera ronda a o a 3 de terminar la segunda

                if ($debug) echo 'Comenzamos con los proyectos en campaña (esto está en '.\LANG.')<br /><br />';

                $projects = Model\Project::getActive($debug);
                foreach ($projects as $project) {
                    self::cron_process_project($project, $debug);
                }

                echo '<hr/>';

            } catch (Exception $e) {

                // mail de aviso
                $mailHandler = new Mail();
                $mailHandler->to = \GOTEO_FAIL_MAIL;
                $mailHandler->subject = 'El cron execute ha dado excepción';
                $mailHandler->content = 'El cron/execute ha dado una excepción. '.$e->getMessage();
                $mailHandler->html = false;
                $mailHandler->template = null;
                $mailHandler->send();
                unset($mailHandler);

            }

        }

        /**
         * Añade evento al feed y manda un mail de aviso
         */
        protected static function cron_warn_no_paypal_account($project) {

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
                $mailHandler->to = (defined('GOTEO_MANAGER_MAIL')) ? \GOTEO_MANAGER_MAIL : \GOTEO_CONTACT_MAIL;
                $mailHandler->toName = 'Goteo.org';
                $mailHandler->subject = 'El proyecto '.$project->name.' no tiene cuenta PayPal';
                $mailHandler->content = 'Hola Goteo, el proyecto '.$project->name.' no tiene cuenta PayPal y el proceso automatico no ha podido ejecutar los preaprovals.';
                $mailHandler->html = false;
                $mailHandler->template = null;
                $mailHandler->send();
                unset($mailHandler);
            }
        }

        /**
         * El proyecto está apunto de acabar. Le quedan 5, 3, 2 ó 1 días
         * Añade evento al feed
         */
        protected static function cron_feed_project_finishing($project) {

            // Evento Feed solo si ejecucion automática
            if (\defined('CRON_EXEC')) {
                $log = new Feed();
                $log->setTarget($project->id);
                $log->populate('proyecto próximo a finalizar ronda (cron)', '/admin/projects',
                    Text::html('feed-project_runout',
                        Feed::item('project', $project->name, $project->id),
                        $project->days,
                        $project->round
                ), $project->image);
                $log->doAdmin('project');

                // evento público
                $log->title = $project->name;
                $log->url = null;
                $log->doPublic('projects');

                unset($log);
            }
        }

        /**
         * El proyecto ha agotado la primera ronda y no ha alcanzado el mínimo.
         * - Pasa a estado caducado.
         * - Añade evento al feed.
         * - Manda emails a todos los relacionados avisando.
         */
        protected static function cron_project_has_failed($project, $per_amount) {
            echo $project->name . ': ha recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . '<br />';
            echo 'No ha conseguido el minimo, cancelamos todos los aportes y lo caducamos:';
            echo '<br />';

            $errors = array();
            if ($project->fail($errors)) {
                $log_text = 'El proyecto %s ha %s obteniendo %s';
            } else {
                @mail(\GOTEO_FAIL_MAIL,
                    'Fallo al archivar ' . SITE_URL,
                    'Fallo al marcar el proyecto '.$project->name.' como archivado ' . implode(',', $errors));
                echo 'ERROR::' . implode(',', $errors);
                $log_text = 'El proyecto %s ha fallado al, %s obteniendo %s';
            }

            // Evento Feed solo si ejecucion automatica
            if (\defined('CRON_EXEC')) {
                $log = new Feed();
                $log->setTarget($project->id);
                $log->populate('proyecto archivado (cron)', '/admin/projects',
                    \vsprintf($log_text, array(
                        Feed::item('project', $project->name, $project->id),
                        Feed::item('relevant', 'caducado sin éxito'),
                        Feed::item('money', $project->amount.' &euro; ('.$per_amount.'&#37;) de aportes sobre minimo')
                )));
                $log->doAdmin('project');

                // evento público
                $log->populate($project->name, null,
                    Text::html('feed-project_fail',
                        Feed::item('project', $project->name, $project->id),
                        $project->amount,
                        $per_amount
                ), $project->image);
                $log->doPublic('projects');
                unset($log);

                //Email de proyecto fallido al autor, inversores y destinatarios de recompensa
                Send::toOwner('fail', $project);
                Send::toInvestors('fail', $project);
                Send::toFriends('fail', $project);
            }
        }

        /**
         * El proyecto ha alcanzado la primera ronda superando el mínimo.
         */
        protected static function cron_project_has_finished_first_round($project, $per_amount) {
            echo $project->name . ': ha recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . '<br />';
            echo 'El proyecto supera la primera ronda: marcamos fecha';

            $errors = array();
            if ($project->passDate($errors)) {
                // se crea el registro de contrato
                if (Model\Contract::create($project->id, $errors)) {
                    echo ' -> Ok:: se ha creado el registro de contrato';
                } else {
                    @mail(\GOTEO_FAIL_MAIL,
                        'Fallo al crear registro de contrato ' . SITE_URL,
                        'Fallo al crear registro de contrato para el proyecto '.$project->name.': ' . implode(',', $errors));
                    echo ' -> semi-Ok: se ha actualiuzado el estado del proyecto pero ha fallado al crear el registro de contrato. ERROR: ' . implode(',', $errors);
                }
            } else {
                @mail(\GOTEO_FAIL_MAIL,
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
                ), $project->image);
                $log->doPublic('projects');
                unset($log);

                // Email de proyecto pasa a segunda ronda al autor y a los inversores
                Send::toOwner('r1_pass', $project);
                Send::toInvestors('r1_pass', $project);

                // mail de aviso
                $mailHandler = new Mail();
                $mailHandler->to = (defined('GOTEO_MANAGER_MAIL')) ? \GOTEO_MANAGER_MAIL : \GOTEO_CONTACT_MAIL;
                $mailHandler->toName = 'Goteo.org';
                $mailHandler->subject = 'Iniciado contrato ' . $project->name;
                $mailHandler->content = 'El proyecto '.$project->name.' ha pasado la primera ronda, se ha iniciado el registro de contrato.';
                $mailHandler->html = false;
                $mailHandler->template = null;
                $mailHandler->send();
                unset($mailHandler);
            }
        }

        /**
         * El proyecto ha finalizado la campaña al ser ronda única
         */
        protected static function cron_project_has_finished_unique_round($project, $per_amount) {
            echo $project->name . ': ha recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . '<br />';
            echo 'El proyecto supera la primera y única ronda: marcamos fecha y damos por financiado';

            // marcamos fecha de pase
            $errors = array();
            if ($project->passDate($errors)) {
                // se crea el registro de contrato
                if (Model\Contract::create($project->id, $errors)) {
                    echo ' -> Ok:: se ha creado el registro de contrato';
                } else {
                    @mail(\GOTEO_FAIL_MAIL,
                        'Fallo al crear registro de contrato ' . SITE_URL,
                        'Fallo al crear registro de contrato para el proyecto '.$project->name.': ' . implode(',', $errors));
                    echo ' -> semi-Ok: se ha actualiuzado el estado del proyecto pero ha fallado al crear el registro de contrato. ERROR: ' . implode(',', $errors);
                }
            } else {
                @mail(\GOTEO_FAIL_MAIL,
                    'Fallo al marcar fecha de sobrepasada primera y única ronda ' . SITE_URL,
                    'Fallo al marcar la fecha de sobrepasada primera y única ronda para el proyecto '.$project->name.': ' . implode(',', $errors));
                echo ' -> ERROR::' . implode(',', $errors);
            }

            // y financiado
            $errors = array();
            if ($project->succeed($errors)) {
                $log_text = 'El proyecto %s ha sido %s obteniendo %s';
            } else {
                @mail(\GOTEO_FAIL_MAIL,
                    'Fallo al marcar financiado ' . SITE_URL,
                    'Fallo al marcar el proyecto '.$project->name.' como financiado ' . implode(',', $errors));
                echo 'ERROR::' . implode(',', $errors);
                $log_text = 'El proyecto %s ha fallado al ser, %s obteniendo %s';
            }

            echo '<br />';

            // Evento Feed solo si ejecucion automatica
            if (\defined('CRON_EXEC')) {
                $log = new Feed();
                $log->setTarget($project->id);
                $log->populate('proyecto supera unica ronda (cron)', '/admin/projects', \vsprintf('El proyecto %s %s su unica ronda obteniendo %s', array(
                    Feed::item('project', $project->name, $project->id),
                    Feed::item('relevant', 'completa'),
                    Feed::item('money', $project->amount.' &euro; ('.\number_format($per_amount, 2).'%) de aportes sobre minimo')
                )));
                $log->doAdmin('project');

                // evento público
                $log->populate($project->name, null,
                    Text::html('feed-project_finish_unique',
                        Feed::item('project', $project->name, $project->id),
                        $project->amount,
                        \round($per_amount)
                    ), $project->image);
                $log->doPublic('projects');
                unset($log);

                // Email de proyecto finaliza su única ronda al autor y a los inversores
                Send::toOwner('unique_pass', $project);
                Send::toInvestors('unique_pass', $project);

                // mail de aviso
                $mailHandler = new Mail();
                $mailHandler->to = (defined('GOTEO_MANAGER_MAIL')) ? \GOTEO_MANAGER_MAIL : \GOTEO_CONTACT_MAIL;
                $mailHandler->toName = 'Goteo.org';
                $mailHandler->subject = 'Iniciado contrato ' . $project->name;
                $mailHandler->content = 'El proyecto '.$project->name.' ha finalizado su única ronda, se ha iniciado el registro de contrato.';
                $mailHandler->html = false;
                $mailHandler->template = null;
                $mailHandler->send();
                unset($mailHandler);
            }
        }

        /**
         * El proyecto ha finalizado la segunda ronda
         */
        protected static function cron_project_has_finished_second_round($project, $per_amount) {
            echo $project->name . ': ha recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . '<br />';
            echo 'Ha llegado a los '.$project->days_total.' días: financiado. ';

            $errors = array();
            if ($project->succeed($errors)) {
                $log_text = 'El proyecto %s ha sido %s obteniendo %s';
            } else {
                @mail(\GOTEO_FAIL_MAIL,
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
                                ), $project->image);
                $log->doPublic('projects');
                unset($log);

                //Email de proyecto final segunda ronda al autor y a los inversores
                Send::toOwner('r2_pass', $project);
                Send::toInvestors('r2_pass', $project);

                // Tareas para gestionar
                // calculamos fecha de passed+90 días
                $passtime = strtotime($project->passed);
                $limsec = date('d/m/Y', \mktime(0, 0, 0, date('m', $passtime), date('d', $passtime)+89, date('Y', $passtime)));

            }
        }

        /**
         * Cancelar los aportes
         * Se llama cuando cancelAll = true
         */
        protected static function cron_cancel_payment($invest, $project, $userData) {

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
                    // se abre la operación en otra ventana
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
        }


        /**
         * Realizar los aportes
         */
        protected static function cron_execute_payment($invest, $project, $userData, $projectAccount) {

            switch ($invest->method) {
                case 'paypal':
                    if (empty($projectAccount->paypal)) {
                        echo '<br />El proyecto '.$project->name.' no tiene cuenta paypal.<br />';
                        Model\Invest::setDetail($invest->id, 'no-paypal-account', 'El proyecto no tiene cuenta paypal en el momento de ejecutar el preapproval. Proceso cron/execute');
                        self::cron_warn_no_paypal_account($project);
                        break;
                    }

                    // cuenta paypal y comisión goteo
                    $invest->account = $projectAccount->paypal;
                    $invest->fee = $projectAccount->fee;
                    $err = array();
                    if (Paypal::pay($invest, $err)) {
                        $log_text = "Se ha ejecutado el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                        echo ' -> Ok';
                        Model\Invest::setDetail($invest->id, 'executed', 'Se ha ejecutado el preapproval, ha iniciado el pago encadenado. Proceso cron/execute');
                        // si era incidencia la desmarcamos
                        if ($invest->issue) {
                            Model\Invest::unsetIssue($invest->id);
                            Model\Invest::setDetail($invest->id, 'issue-solved', 'La incidencia se ha dado por resuelta al ejecutarse correctamente en el proceso automático');
                        }
                    } else {
                        $txt_errors = implode('; ', $err);
                        echo 'Aporte ' . $invest->id . ': Fallo al ejecutar cargo paypal: ' . $txt_errors . '<br />';
                        echo ' -> ERROR!!';
                        Model\Invest::setDetail($invest->id, 'execution-failed', 'Fallo al ejecutar el preapproval, no ha iniciado el pago encadenado: ' . $txt_errors . '. Proceso cron/execute');

                        //  que el sistema NO lance el mensaje a los cofinanciadores
                        // cuando el error lanzado por paypal sea el no estar verificada la cuenta del impulsor
                        if (!isset($err[569042])) {
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
                            $mailHandler->reply = GOTEO_CONTACT_MAIL;
                            $mailHandler->replyName = GOTEO_MAIL_NAME;
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
                                @mail(\GOTEO_FAIL_MAIL,
                                    'Fallo al enviar email de notificacion de incidencia PayPal' . SITE_URL,
                                    'Fallo al enviar email de notificacion de incidencia PayPal: <pre>' . print_r($mailHandler, true). '</pre>');
                            }

                            @mail(\GOTEO_FAIL_MAIL,
                                'Fallo al ejecutar cargo Paypal ' . SITE_URL,
                                'Aporte ' . $invest->id . ': Fallo al ejecutar cargo paypal: <pre>' . print_r($err, true). '</pre>');

                        } else {
                            @mail(\GOTEO_FAIL_MAIL,
                                'Cuenta impulsor no confirmada en paypal ' . SITE_URL,
                                'Aporte ' . $invest->id . ': Fallo al ejecutar cargo paypal: <pre>' . print_r($err, true). '</pre>');
                        }

                    }
                    break;
                case 'tpv':
                    // los cargos con este tpv vienen ejecutados de base
                    echo ' -> Ok';
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
                    // los cargos manuales no los modificamos
                    echo ' Cash, nada que hacer -> Ok';
                    break;
            }
            echo '<br />';

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
                echo $log->html . '<br />';
                unset($log);
            }

        }

        /**
         * Una convocatoria ha finalizado
         * OBSOLETO, ya no se hace esto automático
         *
        protected static function cron_call_has_finished($call) {
            $errors = array();
            echo 'La convocatoria '.$call->name.': ';

            if ($call->checkSuccess($errors)) {
                if ($call->succeed($errors)) {
                    echo 'Ha terminado exitosamente.<br />';

                    $log = new Feed();
                    $log->setTarget($call->id, 'call');
                    $log->unique = true;
                    $log->populate('Campaña terminada (cron)', '/admin/calls/'.$call->id.'?rest='.$call->rest,
                        \vsprintf('La campaña %s ha terminado con exito', array(
                            Feed::item('call', $call->name, $call->id))
                        ));
                    $log->doAdmin('call');
                    $log->populate($call->name, '/call/'.$call->id.'?rest='.$call->rest,
                        \vsprintf('La campaña %s ha terminado con éxito', array(
                            Feed::item('call', $call->name, $call->id))
                        ), $call->logo);
                    $log->doPublic('projects');
                    unset($log);

                } else {
                    echo 'Ha fallado al marcar exitosa.<br />'.implode('<br />', $errors);
                }
            } else {
                echo 'Le queda algun proyecto en primera ronda.<br />';
            }
        }
         */

        /**
         *
         *
        protected static function cron_process_call($call) {
            // tiene que tener presupuesto
            // si le quedan cero
            // -> terminar la campaña exitosamente
            if ($call->rest == 0 && !empty($call->amount))  {
                self::cron_call_has_finished($call);
            }
        }
         */


        /**
         *
         */
        protected static function cron_process_project($project, $debug=false) {
            if ($debug) echo 'Proyecto '.$project->name.'<br />';

            // a ver si tiene cuenta paypal
            $projectAccount = Model\Project\Account::get($project->id);

            $log_text = null;
            $execute = false;
            $cancelAll = false;

            // porcentaje alcanzado
            if ($project->mincost > 0) {
                $per_amount = \floor(($project->amount / $project->mincost) * 100);
            } else {
                $per_amount = 0;
            }

            if ($debug) {

                // días configurados para primera ronda
                echo 'Configurado primera ronda '.$project->days_round1.'  días<br />';

                if ($project->one_round)
                    echo 'Configurado ronda única <br />'; // si configurado ronda unica
                else
                    echo 'Configurado segunda ronda '.$project->days_round2.'  días<br />'; // dias configurado segunda ronda

                // días que lleva en campaña
                echo 'Lleva '.$project->days_active.'  días desde la publicacion<br />';

                // días que le quedan para finalizar esta actual ronda
                echo 'Quedan '.$project->days.' días para el final de la '.$project->round.'a ronda<br />';

                // financiación sobre mínimo
                echo 'Mínimo: '.$project->mincost.' &euro; <br />';
                echo 'Obtenido: '.$project->amount.' &euro;<br />';
                echo 'Ha alcanzado el '.$per_amount.' &#37; del minimo<br />';

            }

            // a los 5, 3, 2, y 1 dia para finalizar ronda
            if ($project->round > 0 && in_array((int) $project->days, array(5, 3, 2, 1))) {
                if ($debug) echo 'Feed publico cuando quedan 5, 3, 2, 1 dias<br />';
                self::cron_feed_project_finishing($project);
            }

            // (financiado a los days_total o cancelado si a los days_round1 no llega al minimo)
            // si ha llegado a los dias configurados para primera ronda:
            //  mínimo-> ejecutar ; no minimo proyecto y todos los preapprovals cancelados
            if ($project->days_active >= $project->days_round1) {
                // si no ha alcanzado el mínimo, pasa a estado caducado
                if ($project->amount < $project->mincost) {
                    if ($debug) echo 'Ha llegado a los '.$project->days_round1.' dias de campaña sin conseguir el minimo, no pasa a segunda ronda<br />';

                    $cancelAll = true;
                    self::cron_project_has_failed($project, $per_amount);

                } else {

                    if ($project->one_round) {
                    // ronda única

                        if ($debug) echo 'Ha llegado a los '.$project->days_round1.' dias de campaña, al ser ronda única termina aquí<br />';
                        $execute = true; // ejecutar los cargos
                        self::cron_project_has_finished_unique_round($project, $per_amount);


                    } elseif ($project->days_active >= $project->days_total) {
                    // tiene hasta el total de días (días primera + días segunda) para conseguir el óptimo (o más)

                        if ($debug) echo 'Ha llegado a los '.$project->days_total.' dias de campaña (final de segunda ronda)<br />';
                        $execute = true; // ejecutar los cargos de la segunda ronda
                        self::cron_project_has_finished_second_round($project, $per_amount);


                    } elseif (empty($project->passed)) {
                    // pasa a segunda ronda

                        if ($debug) echo 'Ha llegado a los '.$project->days_round1.' dias de campaña, pasa a segunda ronda<br />';
                        $execute = true; // ejecutar los cargos de la primera ronda
                        self::cron_project_has_finished_first_round($project, $per_amount);


                    } else {
                        // este caso es lo normal estando en segunda ronda
                        if ($debug) {
                            echo 'Lleva más de '.$project->days_round1.' dias de campaña, debe estar en segunda ronda con fecha marcada<br />';
                            echo $project->name . ': lleva recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . ' y paso a segunda ronda el '.$project->passed.'<br />';
                        }

                    }
                }
            }

            echo '<br />';

            // Tratamiento de los aportes del proyecto actual
            // Si se ha marcado como ejecutar es que ha superado la primera o la segunda ronda (se ejecutará dos veces en cada proyecto)
            // Si se ha marcado como cancelar es que el proyecto no ha superado el mínimo en la primera ronda
            if ($cancelAll || $execute) {
                if ($debug) {
                    echo '::::::Comienza tratamiento de aportes:::::::<br />';
                    echo 'Execute=' . (string) $execute . '  CancelAll=' . (string) $cancelAll . '<br />';
                }

                // tratamiento de aportes pendientes
                $project->invests = Model\Invest::getPending($project->id);

                // Comprueba
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
                        if ($debug) echo 'Cancelando aporte '.$invest->id.' ['.$invest->method.']<br />';
                        self::cron_cancel_payment($invest, $project, $userData);
                    } elseif ($execute && empty($invest->payment)) {
                        // si hay que ejecutar
                        if ($debug) echo 'Ejecutando aporte '.$invest->id.' ['.$invest->method.']';
                        self::cron_execute_payment($invest, $project, $userData, $projectAccount);
                        if ($debug) echo 'Aporte '.$invest->id.' tratado<br />';
                    }

                }

                if ($debug) echo '::Fin tratamiento aportes<br />';
            }

            if ($debug) echo 'Fin tratamiento Proyecto '.$project->name.'<hr />';
        }
    }

}
