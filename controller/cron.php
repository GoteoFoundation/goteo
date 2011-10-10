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
            throw new Redirection('/cron/execute');
        }

        /*
         *  Proceso que ejecuta los cargos, cambia estados, lanza eventos de cambio de ronda
         */
        public function execute () {
            // revision de proyectos: dias, conseguido y cambios de estado
            // proyectos en campaña (y los financiados para ponerle los dias a cero...)
            $projects = Model\Project::active();


//            echo \trace($projects);

            foreach ($projects as &$project) {

                $log_text = null;
                $rest = 0;
                $round = 0;

				// costes y los sumammos
				$project->costs = Model\Project\Cost::getAll($project->id);

                $project->mincost = 0;

                foreach ($project->costs as $item) {
                    if ($item->required == 1) {
                        $project->mincost += $item->amount;
                    }
                }
                
                $execute = false;
                $cancelAll = false;

                // conseguido
                $amount = Model\Invest::invested($project->id);
                if ($project->invested != $amount) {
                    \Goteo\Core\Model::query("UPDATE project SET amount = '{$amount}' WHERE id = ?", array($project->id));
                }

                // los dias que lleva el proyecto  (ojo que los financiados llevaran mas de 80 dias)
                $days = $project->daysActive();

                // actualiza dias restantes para proyectos en campaña
                if ($project->status == 3) {
                    if ($days > 40) {
                        $rest = 80 - $days;
                        $round = 2;
                    } else {
                        $rest = 40 - $days;
                        $round = 1;
                    }

                    if ($rest < 0)
                        $rest = 0;
                }

                if ($project->days != $rest) {
                    \Goteo\Core\Model::query("UPDATE project SET days = '{$rest}' WHERE id = ?", array($project->id));
                }

                // a los 5, 3, 2, y 1 dia para finalizar ronda
                if ($round > 0 && in_array((int) $rest, array(5, 3, 2, 1))) {
                    /*
                     * Evento Feed
                     */
                    $log = new Feed();
                    $log->title = 'proyecto próximo a finalizar ronda (cron)';
                    $log->url = '/admin/projects';
                    $log->type = 'project';
                    $log->html = Text::html('feed-project_runout',
                                    Feed::item('project', $project->name, $project->id),
                                    $rest,
                                    $round
                                    );
                    $log->add($errors);

                    // evento público
                    $log->title = $project->name;
                    $log->url = null;
                    $log->scope = 'public';
                    $log->type = 'projects';
                    $log->add($errors);
                    
                    unset($log);
                }

                // porcentaje alcanzado
                $per_amount = \round(($amount / $project->mincost) * 100);

                // pero seguimos trabajando con el numero de dias que lleva
                echo $project->name . ': lleva recaudado ' . $amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . ' en ' . $days . ' dias, le quedan '.$rest.'<br />';

                // mail al autor a cuando quedan 8 dias, cuando queda 1 día y cuando quedan 20 días
                if ($round == 1 && $rest == 20) {
                    echo 'Email de 20 días en campaña ';
                    if (self::toOwner('20_days', $project)) {
                        echo 'OK';
                    } else {
                        echo 'ERROR';
                    }
                    echo '<br />';
                }
                if ($round == 1 && $rest == 8) {
                    echo 'Email de 8 días para fallar ';
                    if (self::toOwner('8_days', $project)) {
                        echo 'OK';
                    } else {
                        echo 'ERROR';
                    }
                    echo '<br />';
                }
                if ($round == 1 && $rest == 1 && $per_amount > 70) {
                    echo 'Email de 1 día para fallar ';
                    if (self::toOwner('1_day', $project)) {
                        echo 'OK';
                    } else {
                        echo 'ERROR';
                    }
                    echo '<br />';
                }


                //  (financiado a los 80 o cancelado si a los 40 no llega al minimo)
                // si ha llegado a los 40 dias: mínimo-> ejecutar ; no minimo proyecto y todos los preapprovals cancelados
                if ($days >= 40) {
                    // si no ha alcanzado el mínimo, pasa a estado caducado
                    if ($amount < $project->mincost) {

                        echo 'No ha conseguido el minimo, cancelamos todos los aportes y lo caducamos:';
                        $cancelAll = true;
                        $errors = array();
                        if ($project->fail($errors)) {
                            echo 'Caducado.';
                            $log_text = 'El proyecto %s ha %s obteniendo %s';
                        } else {
                            echo 'Falla al caducar ' . implode(',', $errors);
                            $log_text = 'El proyecto %s ha fallado al, %s obteniendo %s';
                        }
                        echo '<br />';
                        
                        /*
                         * Evento Feed
                         */
                        $log = new Feed();
                        $log->title = 'proyecto caducado sin exito (cron)';
                        $log->url = '/admin/projects';
                        $log->type = 'project';
                        $log_items = array(
                            Feed::item('project', $project->name, $project->id),
                            Feed::item('relevant', 'caducado sin éxito'),
                            Feed::item('money', $amount.' &euro; ('.\round($per_amount).'%) de aportes sobre minimo')
                        );
                        $log->html = \vsprintf($log_text, $log_items);
                        $log->add($errors);

                        // evento público
                        $log->title = $project->name;
                        $log->url = null;
                        $log->scope = 'public';
                        $log->type = 'projects';
                        $log->html = Text::html('feed-project_fail',
                                        Feed::item('project', $project->name, $project->id),
                                        $amount,
                                        \round($per_amount)
                                        );
                        $log->add($errors);

                        unset($log);

                        echo 'Email de proyecto fallido al autor ';
                        if (self::toOwner('fail', $project)) {
                            echo 'OK';
                        } else {
                            echo 'ERROR';
                        }
                        echo '<br />';
                        echo 'Email de proyecto fallido a los inversores ';
                        if (self::toInvestors('fail', $project)) {
                            echo 'OK';
                        } else {
                            echo 'ERROR';
                        }
                        echo '<br />';
                        
                        echo '<hr />';
                    } else {
                        // tiene hasta 80 días para conseguir el óptimo (o más)
                        if ($days >= 80) {
                            $execute = true; // ejecutar los cargos de la segunda ronda

                            echo 'Ha llegado a los 80 días: ';
                            $errors = array();
                            if ($project->succeed($errors)) {
                                echo 'Financiado';
                                $log_text = 'El proyecto %s ha sido %s obteniendo %s';
                            } else {
                                echo 'Fallo al marcar financiado ' . implode(',', $errors);
                                $log_text = 'El proyecto %s ha fallado al ser, %s obteniendo %s';
                            }
                            echo '<br />';

                            /*
                             * Evento Feed
                             */
                            $log = new Feed();
                            $log->title = 'proyecto supera segunda ronda (cron)';
                            $log->url = '/admin/projects';
                            $log->type = 'project';
                            $log_items = array(
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('relevant', 'financiado'),
                                Feed::item('money', $amount.' &euro; ('.\round($per_amount).'%) de aportes sobre minimo')
                            );
                            $log->html = \vsprintf($log_text, $log_items);
                            $log->add($errors);

                            // evento público
                            $log->title = $project->name;
                            $log->url = null;
                            $log->scope = 'public';
                            $log->type = 'projects';
                            $log->html = Text::html('feed-project_finish',
                                            Feed::item('project', $project->name, $project->id),
                                            $amount,
                                            \round($per_amount)
                                            );
                            $log->add($errors);

                            unset($log);

                            echo 'Email de proyecto final segunda ronda al autor ';
                            if (self::toOwner('r2_pass', $project)) {
                                echo 'OK';
                            } else {
                                echo 'ERROR';
                            }
                            echo '<br />';
                            echo 'Email de proyecto final segunda ronda a los inversores ';
                            if (self::toInvestors('r2_pass', $project)) {
                                echo 'OK';
                            } else {
                                echo 'ERROR';
                            }
                            echo '<br />';
                            
                            echo '<hr />';
                        } elseif (empty($project->passed)) {
                            $execute = true; // ejecutar los cargos de la primera ronda

                            echo 'El proyecto supera la primera ronda';

                            if ($project->passed()) {
                                echo ', fecha marcada';
                            } else {
                                echo ', fallo al marcar fecha';
                            }

                            echo '<br />';




                            /*
                             * Evento Feed
                             */
                            $log = new Feed();
                            $log->title = 'proyecto supera primera ronda (cron)';
                            $log->url = '/admin/projects';
                            $log->type = 'project';
                            $log_text = 'El proyecto %s %s en segunda ronda obteniendo %s';
                            $log_items = array(
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('relevant', 'continua en campaña'),
                                Feed::item('money', $amount.' &euro; ('.\number_format($per_amount, 2).'%) de aportes sobre minimo')
                            );
                            $log->html = \vsprintf($log_text, $log_items);
                            $log->add($errors);

                            // evento público
                            $log->title = $project->name;
                            $log->url = null;
                            $log->scope = 'public';
                            $log->type = 'projects';
                            $log->html = Text::html('feed-project_goon',
                                            Feed::item('project', $project->name, $project->id),
                                            $amount,
                                            \round($per_amount)
                                            );
                            $log->add($errors);

                            unset($log);

                            echo 'Email de proyecto pasa a segunda ronda al autor ';
                            if (self::toOwner('r1_pass', $project)) {
                                echo 'OK';
                            } else {
                                echo 'ERROR';
                            }
                            echo '<br />';
                            
                            echo 'Email de proyecto pasa a segunda ronda a los inversores ';
                            if (self::toInvestors('r1_pass', $project)) {
                                echo 'OK';
                            } else {
                                echo 'ERROR';
                            }
                            echo '<br />';
                            
                            echo '<hr />';
                        }
                    }
                }

                // a ver si tiene cuenta paypal
                $projectAccount = Model\Project\Account::get($project->id)->paypal;

                if (empty($projectAccount)) {
                    /*
                     * Evento Feed
                     */
                    $log = new Feed();
                    $log->title = 'proyecto sin cuenta paypal (cron)';
                    $log->url = '/admin/projects';
                    $log->type = 'project';
                    $log_text = 'El proyecto %s aun no ha puesto su %s !!!';
                    $log_items = array(
                        Feed::item('project', $project->name, $project->id),
                        Feed::item('relevant', 'cuenta PayPal')
                    );
                    $log->html = \vsprintf($log_text, $log_items);
                    $log->add($errors);

                    unset($log);
                }

                // tratamiento de aportes penddientes
                $query = \Goteo\Core\Model::query("
                    SELECT  *
                    FROM  invest
                    WHERE   invest.status = 0
                    AND     invest.project = ?
                    ", array($project->id));
                $project->invests = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');

                foreach ($project->invests as $key=>&$invest) {

                    $userData = Model\User::getMini($invest->user);

                    echo 'Aporte ' . $invest->id . '<br />';
//                    echo \trace($invest);

                    if ($invest->method == 'paypal' && $invest->invested == date('Y-m-d')) {
                            echo 'Es de hoy.';
                    } elseif ($invest->method != 'cash' && empty($invest->preapproval)) {
                        //si no tiene preaproval, cancelar
                        echo 'Sin preapproval. ';
                        $invest->cancel();
                        continue;
                    }

                    if ($cancelAll) {
                        $invest->setStatus('0');
                        echo 'Aporte pendiente por proyecto caducado.<br />';
                        continue;
                    }

                    // si hay que ejecutar
                    if ($execute && empty($invest->payment)) {

                        // si tiene cuenta, claro...
                        if (empty($projectAccount)) break;

                        echo 'Ejecutando: ';
                        $errors = array();

                        $doFeed = true;

                        switch ($invest->method) {
                            case 'paypal':
                                $invest->account = $projectAccount;
                                if (Paypal::pay($invest, $errors)) {
                                    echo 'Cargo paypal correcto';
                                    $log_text = "Se ha ejecutado el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                                } else {
                                    $txt_errors = implode('; ', $errors);
                                    echo 'Fallo al ejecutar cargo paypal: ' . $txt_errors;
                                    $log_text = "Ha fallado al ejecutar el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                                }
                                break;
                            case 'tpv':
                                if (Tpv::pay($invest, $errors)) {
                                    echo 'Cargo sermepa correcto';
                                    $log_text = "Se ha ejecutado el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s";
                                } else {
                                    $txt_errors = implode('; ', $errors);
                                    echo 'Fallo al ejecutar cargo sermepa: ' . $txt_errors;
                                    $log_text = "Ha fallado al ejecutar el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s <br />Se han dado los siguientes errores: $txt_errors";
                                }
                                break;
                            case 'cash':
                                $invest->setStatus('1');
                                echo 'Aporte al contado, nada que ejecutar.';
                                $doFeed = false;
                                break;
                        }

                        if ($doFeed) {
                            /*
                             * Evento Feed
                             */
                            $log = new Feed();
                            $log->title = 'Cargo ejecutado (cron)';
                            $log->url = '/admin/invests';
                            $log->type = 'system';
                            $log_items = array(
                                Feed::item('user', $userData->name, $userData->id),
                                Feed::item('money', $invest->amount.' &euro;'),
                                Feed::item('system', $invest->id),
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                            );
                            $log->html = \vsprintf($log_text, $log_items);
                            $log->add($errors);
                            unset($log);
                        }

                        echo '<br />';
                    }

                }

                echo '<hr />';
            }

        }


        /*
         *  Proceso que verifica si los preapprovals han sido coancelados
         *   Solamente trata transacciones paypal pendientes de proyectos en campaña
         *
         */
        public function verify () {
            // proyectos en campaña (y los financiados por si se ha quedado alguno descolgado)
            $projects = Model\Project::active();

            foreach ($projects as &$project) {
                $query = Model\Project::query("
                    SELECT  *
                    FROM  invest
                    WHERE   invest.status = 0
                    AND     invest.method = 'paypal'
                    AND     invest.project = ?
                    ", array($project->id));
                $project->invests = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');

                echo "Proyecto: {$project->name} <br />Aportes pendientes: " . count($project->invests) . "<br />";

                foreach ($project->invests as $key=>&$invest) {

                    $details = null;
                    $errors = array();

                    echo 'Aporte ' . $invest->id . '<br />';
/*
 * Aunque sea de hoy, como lo que miramos es que siga activo podemos tratarlo, no?
                    if ($invest->invested == date('Y-m-d')) {
                            // es de hoy, no lo tratamos
                            echo 'Es de hoy<br />';
                    } else
  */
                    if (empty($invest->preapproval)) {
                        // no tiene preaproval, cancelar
                        echo 'No tiene preapproval<br />';
                        $invest->cancel();
                    } else {
                        // comprobar si está cancelado por el usuario
                        if ($details = Paypal::preapprovalDetails($invest->preapproval, $errors)) {

                            // actualizar la cuenta de paypal que se validó para aprobar
                            $invest->setAccount($details->senderEmail);


//                            echo \trace($details);
                            switch ($details->status) {
                                case 'ACTIVE':
                                    echo 'Sigue activo<br />';
                                    break;
                                case 'CANCELED':
                                    echo 'Preapproval cancelado<br />';
                                    $invest->cancel();
                                    break;
                                case 'DEACTIVED':
                                    echo 'Ojo! Desactivado!<br />';
                                    break;
                            }
                        } else {
                            echo 'Errores:<br />' . implode('<br />', $errors);
                        }
                    }

                    echo 'Aporte revisado<hr />';
                }
                
                echo 'Proyecto revisado<hr />';
            }
        }

        /*
         * Realiza los pagos secundarios al proyecto
         *
         * Esto son los aportes de tipo paypal, ejecutados (status 1), que tengan payment code
         *
         */
        public function dopay ($project) {

            $projectData = Model\Project::getMini($project);

            // necesitamos la cuenta del proyecto y que sea la misma que cuando el preapproval
            $projectAccount = Model\Project\Account::get($project);

            if (empty($projectAccount->paypal)) {
                die('El proyecto no tiene la cuenta PayPal!!');
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

            echo 'Vamos a tratar ' . count($invests) . ' aportes<br />';

            foreach ($invests as $key=>$invest) {
                $errors = array();

                $userData = Model\User::getMini($invest->user);
                echo 'Tratando: Aporte (id: '.$invest->id.') de '.$userData->name.'<br />';

                if (Paypal::doPay($invest, $errors)) {
                    echo 'Aporte (id: '.$invest->id.') pagado al proyecto. Ver los detalles en la <a href="/admin/accounts/details/'.$invest->id.'">gestion de transacciones</a><br />';

                    /*
                     * Evento Feed
                     */
                    $log = new Feed();
                    $log->title = 'Pago al proyecto encadenado-secundario (cron)';
                    $log->url = '/admin/accounts';
                    $log->type = 'system';
                    $log_text = "Se ha realizado el pago de %s PayPal al proyecto %s por el aporte de %s (id: %s) del dia %s";
                    $log_items = array(
                        Feed::item('money', $invest->amount.' &euro;'),
                        Feed::item('project', $projectData->name, $project),
                        Feed::item('user', $userData->name, $userData->id),
                        Feed::item('system', $invest->id),
                        Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                    );
                    $log->html = \vsprintf($log_text, $log_items);
                    $log->add($errors);
                    unset($log);

                } else {
                    echo 'Fallo al pagar al proyecto el aporte (id: '.$invest->id.'). Ver los detalles en la <a href="/admin/accounts/details/'.$invest->id.'">gestion de transacciones</a><br />' . implode('<br />', $errors);
                }
                echo '<hr />';
            }


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
                // blind copy a goteo desactivado durante las verificaciones
    //              $mailHandler->bcc = 'comunicaciones@goteo.org';
                $mailHandler->subject = 'En pruebas: '.$subject;
                $mailHandler->content = $content;
                $mailHandler->html = true;
                $mailHandler->template = $template->id;
                return $mailHandler->send();
            }

            return false;
        }

        /* A los cofinanciadores */
        static private function toInvestors ($type, $project) {

            // notificación
            $notif = $type == 'update' ? 'updates' : 'rounds';

            $anyfail = false;
            $tpl = null;

            // para cada inversor que no tenga bloqueado esta notificacion
            $sql = "
                SELECT
                    invest.user as id,
                    user.name as name,
                    user.email as email
                FROM  invest
                INNER JOIN user
                    ON user.id = invest.user
                    AND user.active = 1
                LEFT JOIN user_prefer
                    ON user_prefer.user = invest.user
                WHERE   invest.project = ?
                AND (invest.status = 0 OR invest.status = 1 OR invest.status = 2 )
                AND (user_prefer.{$notif} = 0 OR user_prefer.{$notif} IS NULL)
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

                        case 'fail': // template 17, caduca sin conseguir el mínimo
                                $tpl = 17;
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
                        $template = Template::get($tpl);
                        // Sustituimos los datos
                        $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                        $content = \str_replace($search, $replace, $template->text);
                        // iniciamos mail
                        $mailHandler = new Mail();
                        $mailHandler->to = $project->user->email;
                        // blind copy a goteo desactivado durante las verificaciones
            //              $mailHandler->bcc = 'comunicaciones@goteo.org';
                        $mailHandler->subject = 'En pruebas: '.$subject;
                        $mailHandler->content = $content;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        if ($mailHandler->send()) {

                        } else {
                            $anyfail = true;
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

            // proyectos en campaña o financiados
            $projects = Model\Project::active();

            // para cada uno,
            foreach ($projects as $project) {

//                echo \trace($project->user);
//                continue;
                echo 'Proyecto: ' . $project->name . '<br /><br />';

                // mirar el tiempo desde la última actualización,
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
                echo 'Ultima novedad hace ' . $lastupdate . ' dias<br />';
                if ((int) $lastupdate > 90 || $lastupdate === false) {
                    if ($lastupdate === false) echo 'NEVER! ';
                    echo 'Eso es hace mas de 3 meses<br />';
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
                    echo 'Se le envió hace ' . $lastsend . ' dias<br />';
                    if ($lastsend > 30 || $lastsend === false) {
                        // enviar email no_updates
                        if (self::toOwner('no_updates', $project)) {
                            echo 'enviado no_updates<br />';
                        } else {
                            echo 'fallo al enviar no_updates<br />';
                        }
                    }
                }

                
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
                    FROM message, `comment`
                    WHERE message.project = :project
                    AND comment.user = :owner
                    AND comment.post IN (
                        SELECT post.id
                        FROM post
                        INNER JOIN blog
                            ON  post.blog = blog.id
                            AND blog.type = 'project'
                            AND blog.owner = :project
                        WHERE post.publish = 1
                    )
                    LIMIT 1";
//                echo str_replace(array(':project', ':owner'), array("'{$project->id}'", "'{$project->owner}'"), $sql) . '<br />';
                $query = Model\Project::query($sql, array(':project' => $project->id, ':owner' => $project->owner));
                $lastactivity = $query->fetchColumn(0);
                // si hace más de 3 meses, o nunca ha dicho nada
                echo 'Ultima actividad hace ' . (string) $lastactivity . ' dias<br />';
                if ((int) $lastactivity > 90 || $lastactivity === false) {
                    if ($lastactivity === false) echo 'NEVER! ';
                    echo 'Eso es hace mas de 3 meses<br />';
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
                    echo 'Se le envió hace ' . $lastsend . ' dias<br />';
                    if ($lastsend > 15 || $lastsend === false) {
                        // enviar email no_activity
                        if (self::toOwner('no_activity', $project)) {
                            echo 'enviado no_activity<br />';
                        } else {
                            echo 'fallo al enviar no_activity<br />';
                        }
                    }
                }

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
                echo 'Financiado hace ' . $lastsuccess . ' dias<br />';
                if ($lastsuccess = $query->fetchColumn(0)) {
                    // si hace más de 2 meses
                    if ((int) $lastsuccess > 60) {
                        echo 'Hace mas de dos meses<br />';
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
                        echo 'Se le envió hace ' . (string) $lastsend . ' dias<br />';
                        if ($lastsend > 15 || $lastsend === false) {
                            // enviar email 2m_after
                            if (self::toOwner('2m_after', $project)) {
                                echo 'enviado 2m_after<br />';
                            } else {
                                echo 'fallo al enviar 2m_after<br />';
                            }
                        }
                    }
                }


                // siguiente proyecto
                echo '<hr />';

            }

        }

    }
    
}
