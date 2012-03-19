<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Library\Paypal,
        Goteo\Library\Tpv,
        Goteo\Model;

    class Invests {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

           // reubicando aporte,
           if ($action == 'move') {

                // el aporte original
                $original = Model\Invest::get($id);
                $userData = Model\User::getMini($original->user);
                $projectData = Model\Project::getMini($original->project);

                //el original tiene que ser de tpv o cash y estar como 'cargo ejecutado'
                if ($original->method == 'paypal' || $original->status != 1) {
                    Message::Error('No se puede reubicar este aporte!');
                    throw new Redirection('/admin/invests');
                }


                // generar aporte manual y caducar el original
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['move']) ) {

                    // si falta proyecto, error

                    $projectNew = $_POST['project'];

                    // @TODO a saber si le toca dinero de alguna convocatoria
                    $campaign = null;

                    $invest = new Model\Invest(
                        array(
                            'amount'    => $original->amount,
                            'user'      => $original->user,
                            'project'   => $projectNew,
                            'account'   => $userData->email,
                            'method'    => 'cash',
                            'status'    => '1',
                            'invested'  => date('Y-m-d'),
                            'charged'   => $original->charged,
                            'anonymous' => $original->anonymous,
                            'resign'    => $original->resign,
                            'admin'     => $_SESSION['user']->id,
                            'campaign'  => $campaign
                        )
                    );
                    //@TODO si el proyecto seleccionado

                    if ($invest->save($errors)) {

                        //recompensas que le tocan (si no era resign)
                        if (!$original->resign) {
                            // sacar recompensas
                            $rewards = Model\Project\Reward::getAll($projectNew, 'individual');

                            foreach ($rewards as $rewId => $rewData) {
                                $invest->setReward($rewId); //asignar
                            }
                        }

                        // cambio estado del aporte original a 'Reubicado' (no aparece en cofinanciadores)
                        // si tuviera que aparecer lo marcaríamos como caducado
                        if ($original->setStatus('5')) {
                            // Evento Feed
                            $log = new Feed();
                            $log->populate('Aporte reubicado', '/admin/invests',
                                \vsprintf("%s ha aportado %s al proyecto %s en nombre de %s", array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('money', $_POST['amount'].' &euro;'),
                                    Feed::item('project', $projectData->name, $projectData->id),
                                    Feed::item('user', $userData->name, $userData->id)
                            )));
                            $log->doAdmin('money');
                            unset($log);

                            Message::Info('Aporte reubicado correctamente');
                            throw new Redirection('/admin/invests');
                        } else {
                            $errors[] = 'A fallado al cambiar el estado del aporte original ('.$original->id.')';
                        }
                    } else{
                        $errors[] = 'Ha fallado algo al reubicar el aporte';
                    }

                }

                $viewData = array(
                    'folder' => 'invests',
                    'file' => 'move',
                    'original' => $original,
                    'user'     => $userData,
                    'project'  => $projectData,
                    'errors'   => $errors
                );

                return new View(
                    'view/admin/index.html.php',
                    $viewData
                );

                // fin de la historia dereubicar
           }

            // aportes manuales, cargamos la lista completa de usuarios, proyectos y campañas
           if ($action == 'add') {

                // listado de proyectos existentes
                $projects = Model\Project::getAll();
                // usuarios
                $users = Model\User::getAllMini();
                // campañas
                $calls = Model\Call::getAll();

                //@TODO tema convocatorias (calls)
                //Ojo! Solo convocatorias revisadas?
                //Ojo! Puede ser que se tenga que restringir proyectos de esa

                // generar aporte manual
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add']) ) {

                    $userData = Model\User::getMini($_POST['user']);
                    $projectData = Model\Project::getMini($_POST['project']);

                    $invest = new Model\Invest(
                        array(
                            'amount'    => $_POST['amount'],
                            'user'      => $userData->id,
                            'project'   => $projectData->id,
                            'account'   => $userData->email,
                            'method'    => 'cash',
                            'status'    => '1',
                            'invested'  => date('Y-m-d'),
                            'charged'   => date('Y-m-d'),
                            'anonymous' => $_POST['anonymous'],
                            'resign'    => 1,
                            'admin'     => $_SESSION['user']->id
                        )
                    );

                    //@TODO si llega campaign, montar el $invest->called con instancia call para que el save genere el riego
                    if (!empty($_POST['campaign'])) {
                        $called = Model\Call::get($_POST['campaign']);

                        if ($called instanceof Model\Call) {
                            $invest->called = $called;
                        }
                    }

                    if ($invest->save($errors)) {
                        // Evento Feed
                        $log = new Feed();
                        $log->populate('Aporte manual (admin)', '/admin/invests',
                            \vsprintf("%s ha aportado %s al proyecto %s en nombre de %s", array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('money', $_POST['amount'].' &euro;'),
                                Feed::item('project', $projectData->name, $projectData->id),
                                Feed::item('user', $userData->name, $userData->id)
                        )));
                        $log->doAdmin('money');
                        unset($log);

                        Message::Info('Aporte manual creado correctamente');
                        throw new Redirection('/admin/invests');
                    } else{
                        $errors[] = 'Ha fallado algo al crear el aporte manual';
                    }

                }

                 $viewData = array(
                        'folder' => 'invests',
                        'file' => 'add',
                        'users'         => $users,
                        'projects'      => $projects,
                        'calls'         => $calls,
                        'errors'        => $errors
                    );

                return new View(
                    'view/admin/index.html.php',
                    $viewData
                );

                // fin de la historia

           } else {
                // métodos de pago
                $methods = Model\Invest::methods();
                // estados del proyecto
                $status = Model\Project::status();
                // estados de aporte
                $investStatus = Model\Invest::status();
                // listado de proyectos
                $projects = Model\Invest::projects();
                // usuarios cofinanciadores
                $users = Model\Invest::users(true);
                // campañas que tienen aportes
                $calls = Model\Invest::calls();
                // extras
                $types = array(
                    'donative' => 'Solo los donativos',
                    'anonymous' => 'Solo los anónimos',
                    'manual' => 'Solo los manuales',
                    'campaign' => 'Solo con riego',
                );

           }

            // Informe de la financiación de un proyecto
            if ($action == 'report') {
                // estados de aporte
                $project = Model\Project::get($id);
                if (!$project instanceof Model\Project) {
                    Message::Error('Instancia de proyecto no valida');
                    throw new Redirection('/admin/invests');
                }
                $invests = Model\Invest::getAll($id);
                $project->investors = Model\Invest::investors($id, false, true);
                $users = $project->agregateInvestors();

                // Datos para el informe de transacciones correctas
                $reportData = Model\Invest::getReportData($project->id, $project->status, $project->round, $project->passed);

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'invests',
                        'file' => 'report',
                        'invests' => $invests,
                        'project' => $project,
                        'status' => $status,
                        'users' => $users,
                        'investStatus' => $investStatus,
                        'reportData' => $reportData
                    )
                );
            }

            if (in_array($action, array('details', 'cancel', 'execute')) ) {
                $invest = Model\Invest::get($id);
                $project = Model\Project::get($invest->project);
                $userData = Model\User::get($invest->user);
            }

            // cancelar aporte antes de ejecución, solo aportes no cargados
            if ($action == 'cancel') {

                if ($project->status > 3 && $project->status < 6) {
                    $errors[] = 'No debería poderse cancelar un aporte cuando el proyecto ya está financiado. Si es imprescindible, hacerlo desde el panel de paypal o tpv';
                    break;
                }

                switch ($invest->method) {
                    case 'paypal':
                        $err = array();
                        if (Paypal::cancelPreapproval($invest, $err)) {
                            $errors[] = 'Preaproval paypal cancelado.';
                            $log_text = "El admin %s ha cancelado aporte y preapproval de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                        } else {
                            $txt_errors = implode('; ', $err);
                            $errors[] = 'Fallo al cancelar el preapproval en paypal: ' . $txt_errors;
                            $log_text = "El admin %s ha fallado al cancelar el aporte de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                            if ($invest->cancel()) {
                                $errors[] = 'Aporte cancelado';
                            } else{
                                $errors[] = 'Fallo al cancelar el aporte';
                            }
                        }
                        break;
                    case 'tpv':
                        $err = array();
                        if (Tpv::cancelPreapproval($invest, $err)) {
                            $txt_errors = implode('; ', $err);
                            $errors[] = 'Aporte cancelado correctamente. ' . $txt_errors;
                            $log_text = "El admin %s ha anulado el cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s";
                        } else {
                            $txt_errors = implode('; ', $err);
                            $errors[] = 'Fallo en la operación. ' . $txt_errors;
                            $log_text = "El admin %s ha fallado al solicitar la cancelación del cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                        }
                        break;
                    case 'cash':
                        if ($invest->cancel()) {
                            $log_text = "El admin %s ha cancelado aporte manual de %s de %s (id: %s) al proyecto %s del dia %s";
                            $errors[] = 'Aporte cancelado';
                        } else{
                            $log_text = "El admin %s ha fallado al cancelar el aporte manual de %s de %s (id: %s) al proyecto %s del dia %s. ";
                            $errors[] = 'Fallo al cancelar el aporte';
                        }
                        break;
                }

                // Evento Feed
                $log = new Feed();
                $log->populate('Cargo cancelado manualmente (admin)', '/admin/invests',
                    \vsprintf($log_text, array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('user', $userData->name, $userData->id),
                        Feed::item('money', $invest->amount.' &euro;'),
                        Feed::item('system', $invest->id),
                        Feed::item('project', $project->name, $project->id),
                        Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                )));
                $log->doAdmin();
                unset($log);
            }

            // ejecutar cargo ahora!!, solo aportes no ejecutados
            // si esta pendiente, ejecutar el cargo ahora (como si fuera final de ronda), deja pendiente el pago secundario
            if ($action == 'execute' && $invest->status == 0) {
                switch ($invest->method) {
                    case 'paypal':
                        // a ver si tiene cuenta paypal
                        $projectAccount = Model\Project\Account::get($invest->project);

                        if (empty($projectAccount->paypal)) {
                            // Erroraco!
                            $errors[] = 'El proyecto no tiene cuenta paypal!!, ponersela en la seccion Contrato del dashboard del autor';
                            $log_text = null;

                            // Evento Feed
                            $log = new Feed();
                            $log->populate('proyecto sin cuenta paypal (admin)', '/admin/projects',
                                \vsprintf('El proyecto %s aun no ha puesto su %s !!!', array(
                                    Feed::item('project', $project->name, $project->id),
                                    Feed::item('relevant', 'cuenta PayPal')
                            )));
                            $log->doAdmin('project');
                            unset($log);

                            break;
                        }

                        $invest->account = $projectAccount->paypal;
                        if (Paypal::pay($invest, $errors)) {
                            $errors[] = 'Cargo paypal correcto';
                            $log_text = "El admin %s ha ejecutado el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                            $invest->status = 1;
                        } else {
                            $txt_errors = implode('; ', $errors);
                            $errors[] = 'Fallo al ejecutar cargo paypal: ' . $txt_errors;
                            $log_text = "El admin %s ha fallado al ejecutar el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                        }
                        break;
                    case 'tpv':
                        if (Tpv::pay($invest, $errors)) {
                            $errors[] = 'Cargo sermepa correcto';
                            $log_text = "El admin %s ha ejecutado el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s";
                            $invest->status = 1;
                        } else {
                            $txt_errors = implode('; ', $errors);
                            $errors[] = 'Fallo al ejecutar cargo sermepa: ' . $txt_errors;
                            $log_text = "El admin %s ha fallado al ejecutar el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s <br />Se han dado los siguientes errores: $txt_errors";
                        }
                        break;
                    case 'cash':
                        $invest->setStatus('1');
                        $errors[] = 'Aporte al contado, nada que ejecutar.';
                        $log_text = "El admin %s ha dado por ejecutado el aporte manual a nombre de %s por la cantidad de %s (id: %s) al proyecto %s del dia %s";
                        $invest->status = 1;
                        break;
                }

                if (!empty($log_text)) {
                    // Evento Feed
                    $log = new Feed();
                    $log->populate('Cargo ejecutado manualmente (admin)', '/admin/invests',
                        \vsprintf($log_text, array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('user', $userData->name, $userData->id),
                            Feed::item('money', $invest->amount.' &euro;'),
                            Feed::item('system', $invest->id),
                            Feed::item('project', $project->name, $project->id),
                            Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                    )));
                    $log->doAdmin();
                    unset($log);
                }
            }



            // detalles del aporte
            if (in_array($action, array('details', 'cancel', 'execute')) ) {

                $invest = Model\Invest::get($id);

                if (!empty($invest->droped)) {
                    $droped = Model\Invest::get($invest->droped);
                } else {
                    $droped = null;
                }

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'invests',
                        'file' => 'details',
                        'invest' => $invest,
                        'project' => $project,
                        'user' => $userData,
                        'status' => $status,
                        'investStatus' => $investStatus,
                        'droped' => $droped,
                        'calls' => $calls,
                        'errors' => $errors
                    )
                );
            }

            // listado de aportes
            if ($filters['filtered'] == 'yes') {

                if (!empty($filters['calls']))
                    $filters['types'] = '';

                $list = Model\Invest::getList($filters);
            } else {
                $list = array();
            }

             $viewData = array(
                    'folder' => 'invests',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters,
                    'users'         => $users,
                    'projects'      => $projects,
                    'calls'         => $calls,
                    'methods'       => $methods,
                    'types'         => $types,
                    'status'        => $status,
                    'investStatus'  => $investStatus,
                    'errors'        => $errors
                );

            return new View(
                'view/admin/index.html.php',
                $viewData
            );

        }

    }

}
