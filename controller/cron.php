<?php

namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Core\Error,
        Goteo\Library\Paypal;

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

                echo 'Lleva recaudado ' . $amount . ' de ' . $project->mincost . '/' . $project->maxcost . ' en ' . $days . ' dias<br />';

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
                        // no nos importan los aportes cancelados ni ejecutados
                        continue;
                    }

                    echo 'Aporte ' . $invest->id . '<br />';
//                    echo \trace($invest);

                    $cancelIt = false;

                    if ($invest->method == 'paypal' && $invest->invested == date('Y-m-d')) {
                            echo 'Es de hoy.';
                    } elseif (empty($invest->preapproval)) {
                        //si no tiene preaproval, cancelar
                        echo 'Sin preapproval. ';
                        $cancelIt = true;
                    }

                    if ($cancelAll || $cancelIt) {
                        $invest->cancel();
                        echo 'Cancelado por preapproval no confirmado, falta de preapproval o por poryecto caducado.<br />';
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
                        }

                        echo '<br />';
                    }

                }

                echo '<hr />';
            }

        }

    }
    
}