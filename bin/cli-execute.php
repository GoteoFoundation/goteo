<?php
/**
 * Este es el proceso de final de ronda /cron/execute
 * version linea de comandos
 **/

if (PHP_SAPI !== 'cli') {
    die("Acceso solo por linea de comandos!");
}
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors", 1);
//system timezone
date_default_timezone_set("Europe/Madrid");

use Goteo\Core\Resource,
    Goteo\Core\Error,
    Goteo\Core\Exception,
    Goteo\Model,
    Goteo\Library\Text,
    Goteo\Library\Feed,
    Goteo\Library\Template,
    Goteo\Library\Tpv,
    Goteo\Library\Paypal,
    Goteo\Library\Mail;

require_once __DIR__ . "/../app/config.php";

echo "This script gets active projects and process rounds\n";

// constantes necesarias
define('LANG', 'es');
define('HTTPS_ON', false);
define('SITE_URL', \GOTEO_URL);

// run options
$TEST = false; // throw errors intentionally
if (in_array('--test', $argv)) {
    echo "Testing fail run!\n";
    $TEST = true;
}

// default is dummy run
$UPDATE = false;
if (in_array('--update', $argv)) {
    echo "Real run! Updating the database\n";
    $UPDATE = true;
} else {
    echo "Dummy run! Use the --update modifier to actually update the database \n";
}

$FEED = true; // anyway, only Feed if Update
if (in_array('--no-feed', $argv)) {
    echo "No public feed\n";
    $FEED = false;
} else {
    echo "Public feedback! Use the --no-feed modifier to avoid it \n";
}
// // options

try {

    if ($TEST) {
        echo "throw some errors intentionally to test email sending \n";

        // some tests (yeah, it should be PHPUnit instead....)
        // try a fail_mail
        fail_mail('fail_mail test', print_r($_SERVER, 1));

        // try an Exception mail
        throw new Exception('FORCED EXCEPTION Test');
    }

    // revision de proyectos: dias, conseguido y cambios de estado
    // proyectos en campaña que estén a 5 días de terminar primera ronda a o a 3 de terminar la segunda

    echo "Comenzamos con los proyectos en campaña (esto está en ".\LANG.")\n\n";

    $projects = Model\Project::getActive($FEED);
    foreach ($projects as $project) {
        process_project($project);
        echo "\n---------------------------------------------------------------------------\n";
    }


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





echo " END\n";

///////// Funciones desfactorizadas
/**
 *
 */
function fail_mail($subject, $content)
{
    $subject = "[cli-execute] {$subject} " . \SITE_URL;
    // mail de aviso
    $mailHandler = new Mail();
    $mailHandler->to = \GOTEO_FAIL_MAIL;
    $mailHandler->subject = $subject;
    $mailHandler->content = $content;
    $mailHandler->html = false;
    $mailHandler->template = null;
    $mailHandler->send();
    unset($mailHandler);

    return true;
}

/**
 * Añade evento al feed y manda un mail de aviso
 *
 */
function warn_no_paypal_account($project)
{
    global $FEED;

    if ($FEED) {
        $log = new Feed();
        $log->setTarget($project->id);
        $log->populate('proyecto sin cuenta paypal (cron)', '/admin/projects',
            \vsprintf('El proyecto %s aun no ha puesto su %s !!!', array(
                Feed::item('project', $project->name, $project->id),
                Feed::item('relevant', 'cuenta PayPal')
            )));
        $log->doAdmin('project');
        unset($log);
    }

    // mail de aviso
    $mailHandler = new Mail();
    $mailHandler->to = (defined('GOTEO_MANAGER_MAIL')) ? \GOTEO_MANAGER_MAIL : \GOTEO_CONTACT_MAIL;
    $mailHandler->toName = "Goteo.org";
    $mailHandler->subject = "El proyecto " . $project->name . " no tiene cuenta PayPal";
    $mailHandler->content = "Hola Goteo, el proyecto " . $project->name . " no tiene cuenta PayPal y el proceso automatico no ha podido ejecutar los preaprovals.";
    $mailHandler->html = false;
    $mailHandler->template = null;
    $mailHandler->send();
    unset($mailHandler);

}

/**
 * El proyecto está apunto de acabar. Le quedan 5, 3, 2 ó 1 días
 * Añade evento al feed
 */
function feed_project_finishing($project)
{
    global $FEED;

    // Evento Feed solo si ejecucion automática
    if ($FEED) {
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
function project_fail($project, $per_amount)
{
    global $FEED, $UPDATE;

    echo $project->name . ": ha recaudado " . $project->amount . ", " . $per_amount . "% de " . $project->mincost . "/" . $project->maxcost . "\n";
    echo "No ha conseguido el minimo, cancelamos todos los aportes y lo caducamos:";
    echo "\n";

    $errors = array();
    if ($UPDATE) {
        if ($project->fail($errors)) {
            $log_text = "El proyecto %s ha %s obteniendo %s";
        } else {
            fail_mail('Fallo al archivar proyecto', "Fallo al marcar el proyecto " . $project->name . " como archivado: " . implode(',', $errors));
            echo "ERROR::" . implode(',', $errors);
            $log_text = "El proyecto %s ha fallado al, %s obteniendo %s";
        }

        if ($FEED) {
            $log = new Feed();
            $log->setTarget($project->id);
            $log->populate('proyecto archivado (cron)', '/admin/projects',
                \vsprintf($log_text, array(
                    Feed::item('project', $project->name, $project->id),
                    Feed::item('relevant', 'caducado sin éxito'),
                    Feed::item('money', $project->amount . " &euro; (" . $per_amount . "&#37;) de aportes sobre minimo")
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
            // @FIXME : verificar si esto se puede hacer en cli mode (no veo porque no)
            \Goteo\Controller\Cron\Send::toOwner('fail', $project);
            \Goteo\Controller\Cron\Send::toInvestors('fail', $project);
            \Goteo\Controller\Cron\Send::toFriends('fail', $project);
        }

    } else {
        echo "Prevented project->fail() to query \n # UPDATE project SET status = 6, closed = '".date('Y-m-d')."' WHERE id = '{$project->id}' \n";
    }

}

/**
 * El proyecto ha alcanzado la primera ronda superando el mínimo.
 */
function project_first_round($project, $per_amount)
{
    global $FEED, $UPDATE;

    echo $project->name . ": ha recaudado " . $project->amount . ", " . $per_amount . "% de " . $project->mincost . "/" . $project->maxcost . "\n";
    echo "El proyecto supera la primera ronda: marcamos fecha";

    $errors = array();
    if ($UPDATE) {
        if ($project->passDate($errors)) {
            // se crea el registro de contrato
            if (Model\Contract::create($project->id, $errors)) {
                echo " -> Ok:: se ha creado el registro de contrato\n";

                // mail de aviso
                $mailHandler = new Mail();
                $mailHandler->to = (defined('GOTEO_MANAGER_MAIL')) ? \GOTEO_MANAGER_MAIL : \GOTEO_CONTACT_MAIL;
                $mailHandler->toName = "Goteo.org";
                $mailHandler->subject = "Iniciado contrato " . $project->name;
                $mailHandler->content = "El proyecto " . $project->name . " ha pasado la primera ronda, se ha iniciado el registro de contrato.";
                $mailHandler->html = false;
                $mailHandler->template = null;
                $mailHandler->send();
                unset($mailHandler);

            } else {
                fail_mail('Fallo al crear registro de contrato', "Fallo al crear registro de contrato para el proyecto " . $project->name . ": " . implode(',', $errors));
                echo " -> semi-Ok: se ha actualiuzado el estado del proyecto pero ha fallado al crear el registro de contrato.\n ERROR: " . implode(',', $errors)."\n";
            }
        } else {
            fail_mail('Fallo al marcar fecha de paso a segunda ronda', "Fallo al marcar la fecha de paso a segunda ronda para el proyecto " . $project->name . ": " . implode(',', $errors));
            echo " -> ERROR::" . implode(',', $errors)."\n";
        }


        // Evento Feed solo si Update
        if ($FEED) {
            $log = new Feed();
            $log->setTarget($project->id);
            $log->populate('proyecto supera primera ronda (cron)', '/admin/projects', \vsprintf('El proyecto %s %s en segunda ronda obteniendo %s', array(
                Feed::item('project', $project->name, $project->id),
                Feed::item('relevant', 'continua en campaña'),
                Feed::item('money', $project->amount . " &euro; (" . \number_format($per_amount, 2) . "%) de aportes sobre minimo")
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
            \Goteo\Controller\Cron\Send::toOwner('r1_pass', $project);
            \Goteo\Controller\Cron\Send::toInvestors('r1_pass', $project);
        }

    } else {
        echo "Prevented project->passDate() to query \n # UPDATE project SET passed = '".date('Y-m-d')."' WHERE id = '{$project->id}' \n";
        echo "Prevented Model_Contract::create to initiate the contract for {$project->id} \n";
    }

}

/**
 * El proyecto ha finalizado la campaña al ser ronda única
 */
function project_unique_round($project, $per_amount)
{
    global $FEED, $UPDATE;

    echo $project->name . ": ha recaudado " . $project->amount . ", " . $per_amount . "% de " . $project->mincost . "/" . $project->maxcost . "\n";
    echo "El proyecto supera la primera y única ronda: marcamos fecha y damos por financiado";

    // marcamos fecha de pase
    $errors = array();
    if ($UPDATE) {
        if ($project->passDate($errors)) {
            // se crea el registro de contrato
            if (Model\Contract::create($project->id, $errors)) {
                echo " -> Ok:: se ha creado el registro de contrato";
            } else {
                fail_mail('Fallo al crear registro de contrato', "Fallo al crear registro de contrato para el proyecto " . $project->name . ": " . implode(',', $errors));
                echo " -> semi-Ok: se ha actualiuzado el estado del proyecto pero ha fallado al crear el registro de contrato. ERROR: " . implode(',', $errors);
            }
        } else {
            fail_mail('Fallo al marcar fecha de sobrepasada primera y única ronda', "Fallo al marcar la fecha de sobrepasada primera y única ronda para el proyecto " . $project->name . ": " . implode(',', $errors));
            echo " -> ERROR::" . implode(',', $errors);
        }
    } else {
        echo "Prevented project->passDate() to query \n # UPDATE project SET passed = '".date('Y-m-d')."' WHERE id = '{$project->id}' \n";
        echo "Prevented Model_Contract::create to initiate the contract for {$project->id} \n";
    }

    // y financiado
    $errors = array();
    if ($UPDATE) {
        if ($project->succeed($errors)) {
            $log_text = "El proyecto %s %s su unica ronda obteniendo %s";
        } else {
            fail_mail('Fallo al marcar financiado', "Fallo al marcar el proyecto " . $project->name . " como financiado " . implode(',', $errors));
            echo "ERROR::" . implode(',', $errors);
            $log_text = "El proyecto %s ha dado error cuando %s su unica ronda obteniendo %s";
        }

        // Evento Feed solo si Update
        if ($FEED) {
            $log = new Feed();
            $log->setTarget($project->id);
            $log->populate('proyecto supera unica ronda (cron)', '/admin/projects', \vsprintf($log_text, array(
                Feed::item('project', $project->name, $project->id),
                Feed::item('relevant', 'completa'),
                Feed::item('money', $project->amount . " &euro; (" . \number_format($per_amount, 2) . "%) de aportes sobre minimo")
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
            \Goteo\Controller\Cron\Send::toOwner('unique_pass', $project);
            \Goteo\Controller\Cron\Send::toInvestors('unique_pass', $project);

            // mail de aviso
            $mailHandler = new Mail();
            $mailHandler->to = (defined('GOTEO_MANAGER_MAIL')) ? \GOTEO_MANAGER_MAIL : \GOTEO_CONTACT_MAIL;
            $mailHandler->toName = "Goteo.org";
            $mailHandler->subject = "Iniciado contrato " . $project->name;
            $mailHandler->content = "El proyecto " . $project->name . " ha finalizado su única ronda, se ha iniciado el registro de contrato.";
            $mailHandler->html = false;
            $mailHandler->template = null;
            $mailHandler->send();
            unset($mailHandler);
        }

    } else {
        echo "Prevented project->succeed() to query \n # UPDATE project SET status = 4, success = '".date('Y-m-d')."' WHERE id = '{$project->id}' \n";
    }

    echo "\n";

}

/**
 * El proyecto ha finalizado la segunda ronda
 */
function project_second_round($project, $per_amount)
{
    global $FEED, $UPDATE;

    echo $project->name . ": ha recaudado " . $project->amount . ", " . $per_amount . "% de " . $project->mincost . "/" . $project->maxcost . "\n";
    echo "Ha llegado a los " . $project->days_total . " días: financiado. ";

    $errors = array();
    if ($UPDATE) {
        if ($project->succeed($errors)) {
            $log_text = "El proyecto %s ha sido %s obteniendo %s";
        } else {
            fail_mail('Fallo al marcar financiado', "Fallo al marcar el proyecto " . $project->name . " como financiado " . implode(',', $errors));
            echo "ERROR::" . implode(',', $errors);
            $log_text = "El proyecto %s ha fallado al ser, %s obteniendo %s";
        }

        // Evento Feed y mails solo si Update
        if ($FEED) {
            $log = new Feed();
            $log->setTarget($project->id);
            $log->populate('proyecto supera segunda ronda (cron)', '/admin/projects',
                \vsprintf($log_text, array(
                    Feed::item('project', $project->name, $project->id),
                    Feed::item('relevant', 'financiado'),
                    Feed::item('money', $project->amount . " &euro; (" . \round($per_amount) . "%) de aportes sobre minimo")
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
            \Goteo\Controller\Cron\Send::toOwner('r2_pass', $project);
            \Goteo\Controller\Cron\Send::toInvestors('r2_pass', $project);
        }
    } else {
        echo "Prevented project->succeed() to query \n # UPDATE project SET status = 4, success = '".date('Y-m-d')."' WHERE id = '{$project->id}' \n";
    }
}

/**
 * Cancelar los aportes
 * Se llama cuando cancelAll = true
 */
function cancel_payment($invest, $project, $userData)
{
    global $FEED, $UPDATE;

    echo "Aporte {$invest->id} cancelamos por proyecto caducado ({$invest->method}).\n";
    if ($UPDATE) {
        //  @TODO : this logic will be moved to Library\Payment and every Library\Payment\__Class__
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
                } else {
                    $log_text = "Ha fallado al cancelar el aporte manual de %s de %s (id: %s) al proyecto %s del dia %s. ";
                }
                break;
        }
        if ($FEED) {
            // Evento Feed admin
            $log = new Feed();
            $log->setTarget($project->id);
            $log->populate('Preapproval cancelado por proyecto archivado (cron)', '/admin/invests', \vsprintf($log_text, array(
                Feed::item('user', $userData->name, $userData->id),
                Feed::item('money', $invest->amount . " &euro;"),
                Feed::item('system', $invest->id),
                Feed::item('project', $project->name, $project->id),
                Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
            )));
            $log->doAdmin();
            unset($log);

        }

        echo $log_text."\n";
        $invest->setStatus('4');
        Model\Invest::setDetail($invest->id, 'project-expired', 'Aporte marcado como caducado porque el proyecto no ha tenido exito. Proceso cron/execute');
    } else {
        echo "Prevented cancelation on {$invest->method} \n";
        echo "Prevented invest->setStatus('4') for invest id '{$invest->id}' \n";
        echo "Prevented write detail line \n";
    }
}


/**
 * Realizar los aportes
 */
function execute_payment($invest, $project, $userData, $projectAccount)
{
    global $FEED, $UPDATE;

    // TODO : esto se substituirá por una llamada a Library\Payment ( o a un método de Invest)
    // cada Library\Payment\__Method__Class__ implementará lo que tenga que hacer en

    switch ($invest->method) {
        case 'paypal':

            // Paramos el proceso completamente y lanzamos excepción,
            // si no tiene cuenta paypal y tenemos aportes con paypal
            if (empty($projectAccount->paypal)) {
                echo "Warning! No PayPal account!! /n HALT\n";
                warn_no_paypal_account($project);
                throw new Exception('warn_no_paypal_account -> '.print_r($projectAccount, 1));
            }

            // cuenta paypal y comisión goteo
            $invest->account = $projectAccount->paypal;
            $invest->fee = $projectAccount->fee;
            $err = array();
            if ($UPDATE) {
                if (Paypal::pay($invest, $err)) {
                    $log_text = "Se ha ejecutado el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                    echo " -> Ok\n";
                    Model\Invest::setDetail($invest->id, 'executed', 'Se ha ejecutado el preapproval, ha iniciado el pago encadenado. Proceso cron/execute');
                    // si era incidencia la desmarcamos
                    if ($invest->issue) {
                        Model\Invest::unsetIssue($invest->id);
                        Model\Invest::setDetail($invest->id, 'issue-solved', 'La incidencia se ha dado por resuelta al ejecutarse correctamente en el proceso automático');
                    }


                    if ($FEED) {
                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($project->id);
                        $log->populate('Cargo ejecutado (cron)', '/admin/invests', \vsprintf($log_text, array(
                            Feed::item('user', $userData->name, $userData->id),
                            Feed::item('money', $invest->amount . " &euro;"),
                            Feed::item('system', $invest->id),
                            Feed::item('project', $project->name, $project->id),
                            Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                        )));
                        $log->doAdmin();
                        echo $log->html . "\n";
                        unset($log);
                    }

                } else {
                    $txt_errors = implode('; ', $err);
                    echo "Aporte " . $invest->id . ": Fallo al ejecutar cargo paypal: " . $txt_errors . "\n";
                    echo " -> ERROR!!\n";
                    Model\Invest::setDetail($invest->id, 'execution-failed', 'Fallo al ejecutar el preapproval, no ha iniciado el pago encadenado: ".$txt_errors.". Proceso cron/execute');

                    //  que el sistema NO lance el mensaje a los cofinanciadores
                    // cuando el error lanzado por paypal sea el no estar verificada la cuenta del impulsor
                    if (!isset($err[569042])) {
                        // Notifiacion de incidencia al usuario
                        // Obtenemos la plantilla para asunto y contenido
                        $template = Template::get(37);
                        // Sustituimos los datos
                        $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
                        $search = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%DETAILS%');
                        $replace = array($userData->name, $project->name, SITE_URL . "/project/" . $project->id, $invest->amount, '');
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
                            fail_mail(
                                'Fallo al enviar email de notificacion de incidencia PayPal',
                                "Fallo al enviar email de notificacion de incidencia PayPal: <pre>" . print_r($mailHandler, true) . "</pre>");
                        }

                        fail_mail(
                            'Fallo al ejecutar cargo Paypal',
                            "Aporte " . $invest->id . ": Fallo al ejecutar cargo paypal: <pre>" . print_r($err, true) . "</pre>");

                    } else {
                        fail_mail(
                            'Cuenta impulsor no confirmada en paypal',
                            "Aporte " . $invest->id . ": Fallo al ejecutar cargo paypal: <pre>" . print_r($err, true) . "</pre>");
                    }

                }

            } else {
                echo " prevented Paypal::pay() setDetail and issue treatment \n";
            }

            break;
        case 'tpv':
            // los cargos con este tpv vienen ejecutados de base
            echo " Los aportes de Tpv no necesitan ejecución \n";
            /*
             * Con tpv que permite preapprovals seria:
             * @TODO : esto seria el metodo preapproval en Library\Payment\Tpv
             *
                $err = array();
                if (Tpv::pay($invest, $err)) {
                    echo "Cargo sermepa correcto";
                    $log_text = "Se ha ejecutado el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s";
                } else {
                    $txt_errors = implode('; ', $err);
                    echo "Fallo al ejecutar cargo sermepa: ".$txt_errors;
                    $log_text = "Ha fallado al ejecutar el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s <br />Se han dado los siguientes errores: $txt_errors";
                }
             *
             */
            break;
        case 'cash':
            // los cargos manuales no los modificamos
            echo " Los aportes de Cash no necesitan ejecución \n";
            echo " Cash, nada que hacer -> Ok\n";
            break;
    }


    echo "\n";

}

/**
 *
 */
function process_project($project)
{
    echo "Proyecto '" . $project->name . "'\n";

    // a ver si tiene cuenta paypal
    $projectAccount = Model\Project\Account::get($project->id);

    $log_text = null;

    // porcentaje alcanzado
    if ($project->mincost > 0) {
        $per_amount = \floor(($project->amount / $project->mincost) * 100);
    } else {
        $per_amount = 0;
    }

    {

        // días configurados para primera ronda
        echo "Configurado primera ronda " . $project->days_round1 . "  días\n";

        if ($project->one_round)
            echo "Configurado ronda única \n"; // si configurado ronda unica
        else
            echo "Configurado segunda ronda " . $project->days_round2 . "  días\n"; // dias configurado segunda ronda

        // días que lleva en campaña
        echo "Lleva " . $project->days_active . "  días desde la publicacion\n";

        // días que le quedan para finalizar esta actual ronda
        echo "Quedan " . $project->days . " días para el final de la " . $project->round . "a ronda\n";

        // financiación sobre mínimo
        echo "Mínimo: " . $project->mincost . " eur\n";
        echo "Obtenido: " . $project->amount . " eur\n";
        echo "Ha alcanzado el " . $per_amount . " % del minimo\n";

    }

    // a los 5, 3, 2, y 1 dia para finalizar ronda
    if ($project->round > 0 && in_array((int)$project->days, array(5, 3, 2, 1))) {
        echo "Feed publico cuando quedan 5, 3, 2, 1 dias\n";
        feed_project_finishing($project);
    }

    // (financiado a los days_total o cancelado si a los days_round1 no llega al minimo)
    // si ha llegado a los dias configurados para primera ronda:
    //  mínimo-> ejecutar ; no minimo proyecto y todos los preapprovals cancelados
    if ($project->days_active >= $project->days_round1) {
        // si no ha alcanzado el mínimo, pasa a estado caducado
        if ($project->amount < $project->mincost) {
            echo "Ha llegado a los " . $project->days_round1 . " dias de campaña sin conseguir el minimo, no pasa a segunda ronda\n";

            // devolver los aportes
            process_invests($project, $projectAccount, 'cancel');
            project_fail($project, $per_amount);

        } else {

            if ($project->one_round) {
                // ronda única

                echo "Ha llegado a los " . $project->days_round1 . " dias de campaña, al ser ronda única termina aquí\n";
                // ejecutar los cargos
                process_invests($project, $projectAccount, 'execute');
                project_unique_round($project, $per_amount);


            } elseif ($project->days_active >= $project->days_total) {
                // tiene hasta el total de días (días primera + días segunda) para conseguir el óptimo (o más)

                echo "Ha llegado a los " . $project->days_total . " dias de campaña (final de segunda ronda)\n";
                // ejecutar los cargos de la segunda ronda (y los que quedaran de primera ronda)
                process_invests($project, $projectAccount, 'execute');
                project_second_round($project, $per_amount);


            } elseif (empty($project->passed)) {
                // pasa a segunda ronda

                echo "Ha llegado a los " . $project->days_round1 . " dias de campaña, pasa a segunda ronda\n";
                // ejecutar los cargos de la primera ronda
                process_invests($project, $projectAccount, 'execute');
                project_first_round($project, $per_amount);


            } else {
                // este caso es lo normal estando en segunda ronda
                {
                    echo "Lleva más de " . $project->days_round1 . " dias de campaña, debe estar en segunda ronda con fecha marcada\n";
                    echo $project->name . ": lleva recaudado " . $project->amount . ", " . $per_amount . "% de " . $project->mincost . "/" . $project->maxcost . " y paso a segunda ronda el " . $project->passed . "\n";
                }

            }
        }
    }

    echo "\n";


    echo "Fin tratamiento Proyecto " . $project->name . "\n";
}

/**
 * @param null $process (null, '', 'cancel', 'execute'
 * // Tratamiento de los aportes del proyecto actual
 * // Si se ha marcado como ejecutar es que ha superado la primera o la segunda ronda (se ejecutará dos veces en cada proyecto)
 * // Si se ha marcado como cancelar es que el proyecto no ha superado el mínimo en la primera ronda
 */
function process_invests($project, $projectAccount, $process = null)
{

    global $FEED, $UPDATE;

    if (empty($process) || !in_array($process, array('cancell', 'execute'))) {
        echo "Nada que tratar. Process = '{$process}' \n";
        return true;
    }

    echo "::::::Comienza tratamiento de aportes:::::::\n";
    echo "Process = " . $process . " \n";


    // tratamiento de aportes pendientes
    // @TODO : la distinción de metodos de pago y estado no debería hacerse así como se hace
    // métodos a piñon deberia pasarse a Library\Payment
    $project->invests = Model\Invest::getPending($project->id);

    // Comprueba
    foreach ($project->invests as $key => $invest) {
        $errors = array();
        $log_text = null;

        $userData = Model\User::getMini($invest->user);

        // revisión de código preapproval
        if ($invest->method == 'paypal' && empty($invest->preapproval)) {
            // @TODO : esto también va en la capa de abstracción de pagos
            // esta verificación debería pasarse a la instancia invest y que cada pasarela decida si debe tener código o no (quizás segun estado, tipo)
            // y que la instancia misma se cancele y grabe la line ade detalle

            //si no tiene preaproval, cancelar
            echo "Aporte " . $invest->id . " cancelado por no tener preapproval.\n";
            if ($UPDATE) {
                $invest->cancel();
                Model\Invest::setDetail($invest->id, 'no-preapproval', 'Aporte cancelado porque no tiene preapproval. Proceso cron/execute');
            } else {
                echo " Prevented invest->cancel() and Model\\Invest::setDetail";
            }
            continue;
        }

        if ($process == 'cancel') {
            echo "Cancelando aporte " . $invest->id . " [" . $invest->method . "]\n";
            // @TODO : si invest.credit , no cancelar
            // crear crédito
            cancel_payment($invest, $project, $userData);

        } elseif ($process == 'execute' && empty($invest->payment)) {
            // si hay que ejecutar
            echo "Ejecutando aporte " . $invest->id . " [" . $invest->method . "]\n";
            // @TODO : si invest.credit , no ejecutar (ya pagado)
            // marcar como credito reubicado
            execute_payment($invest, $project, $userData, $projectAccount);
            
        }

        echo "Aporte " . $invest->id . " tratado con {$process}\n";

    }
    echo "::Fin tratamiento aportes\n";

    return true;

}
