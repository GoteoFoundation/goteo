<?php

namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Library\Text,
        Goteo\Library\Paypal,
		Goteo\Library\Feed,
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

                // pero seguimos trabajando con el numero de dias que lleva
                echo $project->name . ': lleva recaudado ' . $amount . ' de ' . $project->mincost . '/' . $project->maxcost . ' en ' . $days . ' dias, le quedan '.$rest.'<br />';

                //  (financiado a los 80 o cancelado si a los 40 no llega al minimo)
                // porcentaje alcanzado
                $per_amount = ($amount / $project->mincost) * 100;
                $per_amount = \round($per_amount);

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

                        echo '<br />';
                    } else {
                        $execute = true; // mas de 40 sin caducar es ejecutar el cargo

                        // tiene hasta 80 días para conseguir el óptimo (o más)
                        if ($days >= 80) {
                            echo 'Ha llegado a los 80 días: ';
                            $errors = array();
                            if ($project->succeed($errors)) {
                                echo 'Financiado';
                                $log_text = 'El proyecto %s ha sido %s obteniendo %s';
                            } else {
                                echo 'Fallo al marcar financiado ' . implode(',', $errors);
                                $log_text = 'El proyecto %s ha fallado al ser, %s obteniendo %s';
                            }

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
                            echo '<br />';
                        } else {
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
                            echo '<br />';
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
                        echo 'Aporte pendiente por poryecto caducado.<br />';
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



    }
    
}


/*
 * Mensaje a los cofinanciadores de un proyecto si este falla
 *
function failNotice ($project) {

    echo "Mensaje a los cofinanciadores de un proyecto fallido '{$project}'<br />";

    $project = Model\Project::get($project);

    $sql = "
        SELECT  DISTINCT(user) as id
        FROM    invest
        WHERE   project = ?
        AND status = 0";

    $query = Model::query($sql, array($project->id));

    while ($row = $query->fetchObject()) {
        echo "Cofinanciador: {$row->id}<br />";

        continue;
        
        // Email de recuperacion
        $mail = new Mail();
        $mail->to = $row->email;
        $mail->toName = $row->name;
        $mail->subject = 'El proyecto ';
        $url = SITE_URL . '/user/recover/' . base64_encode($token);
        $mail->content = sprintf('
            Estimado(a) <strong>%1$s</strong>:<br/>
            <br/>
            Hemos recibido una petición para recuperar la contraseña de tu cuenta de usuario en Goteo.org<br />
            Si no has solicitado esta recuperación de contraseña, ignora este mensaje<br />
            Para acceder a tu cuenta y cambiar la contraseña (utilice su nombre de usuario como contraseña actual), utiliza el siguiente enlace. Si no puedes hacer click, copialo y pegalo en el navegador.
            <br/>
            <a href="%2$s">%2$s</a><br/>
            <br/>
            Recuerde que su nombre de usuario es <strong>%3$s</strong>, póngalo como contraseña actual para cambiar la contraseña.<br/>
            Hasta pronto!
        ', $row->name, $url, $row->id);
        $mail->html = true;
        if ($mail->send($errors)) {
            return true;
        }
    }
    return false;
}
 * 
*/
