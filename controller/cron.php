<?php

namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Core\Error,
        Goteo\Library\Text,
        Goteo\Library\Paypal,
        Goteo\Library\Tpv;

    class Cron extends \Goteo\Core\Controller {
        
        public function index () {

            // revision de proyectos: dias, conseguido y cambios de estado
            // proyectos en campaña o financiados
            $projects = Model\Project::invested();


//            echo \trace($projects);

            foreach ($projects as &$project) {

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
                    if ($days > 40)
                        $rest = 80 - $days;
                    else
                        $rest = 40 - $days;

                    if ($rest < 0)
                        $rest = 0;

                    if ($project->days != $rest) {
                        \Goteo\Core\Model::query("UPDATE project SET days = '{$rest}' WHERE id = ?", array($project->id));
                    }
                }
                // pero aqui seguimos trabajando con el numero de dias que lleva

                echo $project->name . ': lleva recaudado ' . $amount . ' de ' . $project->mincost . '/' . $project->maxcost . ' en ' . $days . ' dias, le quedan '.$rest.'<br />';

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
                        } else {
                            echo 'Falla al caducar ' . implode(',', $errors);
                        }
                        echo '<br />';
                    } else {
                        $execute = true; // mas de 40 sin caducar es ejecutar el cargo

                        // tiene hasta 80 días para conseguir el óptimo (o más)
                        if ($days >= 80) {
                            echo 'Ha llegado a los 80 días: ';
                            $errors = array();
                            if ($project->succeed($errors)) {
                                echo 'Financiado';
                            } else {
                                echo 'Fallo al marcar financiado ' . implode(',', $errors);
                            }
                            echo '<br />';
                        }
                    }
                }


                // tratamiento de aportes
                $query = \Goteo\Core\Model::query("
                    SELECT  *
                    FROM  invest
                    WHERE   invest.project = ?
                    ", array($project->id));
                $project->invests = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');

                // para cada uno sacar todos sus aportes
                foreach ($project->invests as $key=>&$invest) {

                    if ($invest->status != 0) {
                        // no nos importan los aportes cancelados ni ejecutados ni en proceso
                        continue;
                    }

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
                        echo 'Ejecutando: ';
                        $errors = array();

                        switch ($invest->method) {
                            case 'paypal':
                                if (Paypal::pay($invest, $errors)) {
                                    echo 'Cargo paypal correcto';
                                } else {
                                    echo 'Fallo al ejecutar cargo paypal: ' . implode('; ', $errors);
                                }
                                break;
                            case 'tpv':
                                if (Tpv::pay($invest, $errors)) {
                                    echo 'Cargo sermepa correcto';
                                } else {
                                    echo 'Fallo al ejecutar cargo sermepal: ' . implode('; ', $errors);
                                }
                                break;
                            case 'cash':
                                echo 'Aporte al contado, nada que ejecutar.';
                                break;
                        }

                        echo '<br />';
                    }

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
