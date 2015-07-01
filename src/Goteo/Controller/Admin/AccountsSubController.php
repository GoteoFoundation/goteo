<?php
/**
 * Gestion completa de aportes para nodo central solo
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Tpv,
	Goteo\Library\Paypal,
	Goteo\Library\Feed,
    Goteo\Application\Message,
    Goteo\Application\Config,
	Goteo\Application\Session,
    Goteo\Model;

class AccountsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Aporte manual',
      'move' => 'Reubicando el aporte',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe de proyecto',
      'viewer' => 'Viendo logs',
    );


    static protected $label = 'Aportes';


    protected $filters = array (
      'id' => '',
      'methods' => '',
      'investStatus' => 'all',
      'projects' => '',
      'name' => '',
      'calls' => '',
      'review' => '',
      'types' => '',
      'date_from' => '',
      'date_until' => '',
      'issue' => 'all',
      'procStatus' => 'all',
      'amount' => '',
      'maxamount' => '',
    );


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node allowed here
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function viewerAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('viewer', $id, $this->getFilters(), $subaction));
    }


    public function reportAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('report', $id, $this->getFilters(), $subaction));
    }


    public function cancelAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('cancel', $id, $this->getFilters(), $subaction));
    }


    public function executeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('execute', $id, $this->getFilters(), $subaction));
    }


    public function moveAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('move', $id, $this->getFilters(), $subaction));
    }


    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }


    public function updateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('update', $id, $this->getFilters(), $subaction));
    }

    public function solveupdateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('solve', $id, $this->getFilters(), $subaction));
    }


    public function detailsAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('details', $id, $this->getFilters(), $subaction));
    }


    public function resignAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('resign', $id, $this->getFilters(), $subaction));
    }

    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }

    public function process ($action = 'list', $id = null, $filters = array()) {

        $errors = array();

       // reubicando aporte,
       if ($action == 'move') {

            // el aporte original
            $original = Model\Invest::get($id);
            $userData = Model\User::getMini($original->user);
            $projectData = Model\Project::getMini($original->project);

            //el original tiene que ser de tpv o cash y estar como 'cargo ejecutado'
            if ($original->method == 'paypal' || $original->status != 1) {
                Message::error('No se puede reubicar este aporte!');
                return $this->redirect('/admin/accounts');
            }


            // generar aporte manual y caducar el original
            if ($this->isPost() && $this->hasPost('move'))  {

                // si falta proyecto, error

                $projectNew = $this->getPost('project');

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
                        'admin'     => $this->user->id,
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

                        // mantenimiento de registros relacionados (usuario, proyecto, ...)
                        $original->keepUpdated();

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($projectData->id);
                        $log->populate('Aporte reubicado', '/admin/accounts',
                            \vsprintf("%s ha aportado %s al proyecto %s en nombre de %s", array(
                                Feed::item('user', $this->user->name, $this->user->id),
                                Feed::item('money', $invest->amount.' &euro;'),
                                Feed::item('project', $projectData->name, $projectData->id),
                                Feed::item('user', $userData->name, $userData->id)
                        )));
                        $log->doAdmin('money');
                        unset($log);

                        Message::info('Aporte reubicado correctamente');
                        return $this->redirect('/admin/accounts');
                    } else {
                        $errors[] = 'A fallado al cambiar el estado del aporte original ('.$original->id.')';
                    }
                } else{
                    $errors[] = 'Ha fallado algo al reubicar el aporte';
                }

            }

            $viewData = array(
                'folder' => 'accounts',
                'file' => 'move',
                'original' => $original,
                'user'     => $userData,
                'project'  => $projectData
            );

            return $viewData;

            // fin de la historia dereubicar
       }

       // cambiando estado del aporte aporte,
       if ($action == 'update') {

            // el aporte original
            $invest = Model\Invest::get($id);
            if (!$invest instanceof Model\Invest) {
                Message::error('No tenemos registro del aporte '.$id);
                return $this->redirect('/admin/accounts');
            }

            $status = Model\Invest::status();

            $new = $this->hasPost('status') ? $this->getPost('status') : null;

            if ($this->isPost() && $this->hasPost('update')) {

                // si estan desmarcando incidencia
                if ($invest->issue && $this->getPost('resolve') == 1) {
                    Model\Invest::unsetIssue($id);
                    Model\Invest::setDetail($id, 'issue-solved', 'La incidencia se ha dado por resuelta por el usuario ' . $this->user->name);
                    Message::info('La incidencia se ha dado por resuelta');
                }

                if ($new != $invest->status && isset($new) && isset($status[$new])) {
                    if (Model\Invest::query("UPDATE invest SET status=:status WHERE id=:id", array(':id'=>$id, ':status'=>$new))) {
                        Model\Invest::setDetail($id, 'status-change'.rand(0, 9999), 'El admin ' . $this->user->name . ' ha cambiado el estado a '.$status[$new]);
                        Message::info('Se ha actualizado el estado del aporte');
                    } else {
                        Message::error('Ha fallado al actualizar el estado del aporte');
                    }
                }

                // mantenimiento de registros relacionados (usuario, proyecto, ...)
                $invest->keepUpdated();

                return $this->redirect('/admin/accounts/details/'.$id);
            }

            return array(
                'folder' => 'accounts',
                'file' => 'update',
                'invest' => $invest,
                'status' => $status
            );

            // fin de la historia actualizar estado
       }

       // resolviendo incidencias
       if ($action == 'solve') {

            // el aporte original
            $invest = Model\Invest::get($id);
            if (!$invest instanceof Model\Invest) {
                Message::error('No tenemos registro del aporte '.$id);
                return $this->redirect('/admin/accounts');
            }
            $projectData = Model\Project::getMini($invest->project);
            $userData =  Model\User::get($invest->user);

            $errors = array();

            // primero cancelar
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
            $log->setTarget($projectData->id);
            $log->populate('Cargo cancelado al resolver (admin)', '/admin/accounts',
                \vsprintf($log_text, array(
                    Feed::item('user', $this->user->name, $this->user->id),
                    Feed::item('user', $userData->name, $userData->id),
                    Feed::item('money', $invest->amount.' &euro;'),
                    Feed::item('system', $invest->id),
                    Feed::item('project', $projectData->name, $projectData->id),
                    Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
            )));
            $log->doAdmin('admin');
            unset($log);

            // luego resolver
            if ($invest->solve($errors)) {
                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Incidencia resuelta (admin)', '/admin/accounts',
                    \vsprintf("El admin %s ha dado por resuelta la incidencia con el botón \"Nos han hecho la transferencia\" para el aporte %s", array(
                        Feed::item('user', $this->user->name, $this->user->id),
                        Feed::item('system', $id, 'accounts/details/'.$id)
                )));
                $log->doAdmin('admin');
                unset($log);

                // mantenimiento de registros relacionados (usuario, proyecto, ...)
                $invest->keepUpdated();

                Message::info('La incidencia se ha dado por resuelta, el aporte se ha pasado a manual y cobrado');
                return $this->redirect('/admin/accounts');
            } else {
                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Fallo al resolver incidencia (admin)', '/admin/accounts',
                    \vsprintf("Al admin %s le ha fallado el botón \"Nos han hecho la transferencia\" para el aporte %s", array(
                        Feed::item('user', $this->user->name, $this->user->id),
                        Feed::item('system', $id, 'accounts/details/'.$id)
                )));
                $log->doAdmin('admin');
                unset($log);

                Message::error('Ha fallado al resolver la incidencia: ' . implode (',', $errors));
                return $this->redirect('/admin/accounts/details/'.$id);
            }
       }

        // aportes manuales, cargamos la lista completa de usuarios, proyectos y campañas
       if ($action == 'add') {

            // listado de proyectos en campaña
            $projects = Model\Project::active(false, true);
            // usuarios
            $users = Model\User::getAllMini();
            // campañas
            $calls = Model\Call::getAll();


            // generar aporte manual
            if ($this->isPost() && $this->hasPost('add') ) {

                $userData = Model\User::getMini($this->getPost('user'));
                $projectData = Model\Project::getMini($this->getPost('project'));

                $invest = new Model\Invest(
                    array(
                        'amount'    => $this->getPost('amount'),
                        'user'      => $userData->id,
                        'project'   => $projectData->id,
                        'account'   => $userData->email,
                        'method'    => 'cash',
                        'status'    => '1',
                        'invested'  => date('Y-m-d'),
                        'charged'   => date('Y-m-d'),
                        'anonymous' => $this->getPost('anonymous'),
                        'resign'    => 1,
                        'admin'     => $this->user->id
                    )
                );

                // si llega campaign, montar el $invest->called con instancia call para que el save genere el riego
                if (!empty($this->getPost('campaign'))) {
                    $called = Model\Call::getMini($this->getPost('campaign'));

                    if ($called instanceof Model\Call) {
                        $invest->called = $called;
                    }
                }

                if ($invest->save($errors)) {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($projectData->id);
                    $log->populate('Aporte manual (admin)', '/admin/accounts',
                        \vsprintf("%s ha aportado %s al proyecto %s en nombre de %s", array(
                            Feed::item('user', $this->user->name, $this->user->id),
                            Feed::item('money', $invest->amount.' &euro;'),
                            Feed::item('project', $projectData->name, $projectData->id),
                            Feed::item('user', $userData->name, $userData->id)
                    )));
                    $log->doAdmin('money');
                    unset($log);

                    Model\Invest::setDetail($invest->id, 'admin-created', 'Este aporte ha sido creado manualmente por el admin ' . $this->user->name);
                    Message::info('Aporte manual creado correctamente, seleccionar recompensa y dirección de entrega.');
                    return $this->redirect('/admin/rewards/edit/'.$invest->id);
                } else{
                    $errors[] = 'Ha fallado algo al crear el aporte manual';
                }

            }

             $viewData = array(
                    'folder' => 'accounts',
                    'file' => 'add',
                    'autocomplete'  => true,
                    'users'         => $users,
                    'projects'      => $projects,
                    'calls'         => $calls
                );

            return $viewData;

            // fin de la historia

       }

        // Informe de la financiación de un proyecto
        if ($action == 'report') {
            $project = Model\Project::get($id);
            if (!$project instanceof Model\Project) {
                Message::error('Instancia de proyecto no valida');
                return $this->redirect('/admin/accounts');
            }
            $invests = Model\Invest::getAll($id);
            $project->investors = Model\Invest::investors($id, false, true);
            $users = $project->agregateInvestors();
            $status = Model\Project::status();
            $investStatus = Model\Invest::status();

            // Datos para el informe de transacciones correctas
            $Data = Model\Invest::getReportData($project->id, $project->status, $project->round, $project->passed);
            $account = Model\Project\Account::get($project->id);

            return array(
                    'folder' => 'accounts',
                    'file' => 'report',
                    'invests' => $invests,
                    'project' => $project,
                    'account' => $account,
                    'status' => $status,
                    'users' => $users,
                    'investStatus' => $investStatus,
                    'Data' => $Data
            );
        }

        // cancelar aporte antes de ejecución, solo aportes no cargados
        if ($action == 'cancel') {
            $invest = Model\Invest::get($id);
            if (!$invest instanceof Model\Invest) {
                Message::error('No tenemos objeto para el aporte '.$id);
                return $this->redirect('/admin/accounts');
            }
            $project = Model\Project::get($invest->project);
            $userData = Model\User::get($invest->user);

            if ($project->status > 3 && $project->status < 6) {
                $errors[] = 'No debería poderse cancelar un aporte cuando el proyecto ya está financiado. Si es imprescindible, hacerlo desde el panel de paypal o tpv';
            } else {

                switch ($invest->method) {
                    case 'paypal':
                        $err = array();

                        if (empty($invest->preapproval)) {

                            if (Paypal::cancelPay($invest, $err)) {
                                $errors[] = 'Pago PayPal paypal cancelado.';
                                $log_text = "El admin %s ha cancelado aporte y pago PayPal de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                            } else {
                                $txt_errors = implode('; ', $err);
                                $errors[] = 'Fallo al cancelar el pago PayPal: ' . $txt_errors;
                                $log_text = "El admin %s ha fallado al cancelar el aporte de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                                if ($invest->cancel()) {
                                    $errors[] = 'Aporte cancelado';
                                } else{
                                    $errors[] = 'Fallo al cancelar el aporte';
                                }
                            }

                        } else {

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
                $log->setTarget($project->id);
                $log->populate('Cargo cancelado manualmente (admin)', '/admin/accounts',
                    \vsprintf($log_text, array(
                        Feed::item('user', $this->user->name, $this->user->id),
                        Feed::item('user', $userData->name, $userData->id),
                        Feed::item('money', $invest->amount.' &euro;'),
                        Feed::item('system', $invest->id),
                        Feed::item('project', $project->name, $project->id),
                        Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                )));
                $log->doAdmin('admin');
                Model\Invest::setDetail($invest->id, 'manually-canceled', $log->html);
                unset($log);

            }


            // mantenimiento de registros relacionados (usuario, proyecto, ...)
            $invest->keepUpdated();
        }

        // ejecutar cargo ahora!!, solo aportes no ejecutados
        // si esta pendiente, ejecutar el cargo ahora (como si fuera final de ronda), deja pendiente el pago secundario
        if ($action == 'execute' && $invest->status == 0) {
            $invest = Model\Invest::get($id);
            if (!$invest instanceof Model\Invest) {
                Message::error('No tenemos objeto para el aporte '.$id);
                return $this->redirect('/admin/accounts');
            }
            $project = Model\Project::get($invest->project);
            $userData = Model\User::get($invest->user);

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
                        $log->setTarget($project->id);
                        $log->populate('proyecto sin cuenta paypal (admin)', '/admin/projects',
                            \vsprintf('El proyecto %s aun no ha puesto su %s !!!', array(
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('relevant', 'cuenta PayPal')
                        )));
                        $log->doAdmin('project');
                        unset($log);

                        break;
                    }

                    // cuenta paypal y comisión goteo
                    $invest->account = $projectAccount->paypal;
                    $invest->fee = $projectAccount->fee;
                    if (Paypal::execute($invest, $errors)) {
                        $errors[] = 'Cargo paypal correcto';
                        $log_text = "El admin %s ha ejecutado el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                        $invest->status = 1;

                        // si era incidencia la desmarcamos
                        if ($invest->issue) {
                            Model\Invest::unsetIssue($invest->id);
                            Model\Invest::setDetail($invest->id, 'issue-solved', 'La incidencia se ha dado por resuelta al ejecutar el aporte manualmente por el admin ' . $this->user->name);
                        }


                    } else {
                        $txt_errors = implode('; ', $errors);
                        $errors[] = 'Fallo al ejecutar cargo paypal: ' . $txt_errors . '<strong>POSIBLE INCIDENCIA NO COMUNICADA Y APORTE NO CANCELADO, HAY QUE TRATARLA MANUALMENTE</strong>';
                        $log_text = "El admin %s ha fallado al ejecutar el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                    }
                    break;
                case 'tpv':
                    // no tiene sentido ejecutar así un aporte tpv que ya está cobrado
                    if (Tpv::execute($invest, $errors)) {
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
                $log->setTarget($project->id);
                $log->populate('Cargo ejecutado manualmente (admin)', '/admin/accounts',
                    \vsprintf($log_text, array(
                        Feed::item('user', $this->user->name, $this->user->id),
                        Feed::item('user', $userData->name, $userData->id),
                        Feed::item('money', $invest->amount.' &euro;'),
                        Feed::item('system', $invest->id),
                        Feed::item('project', $project->name, $project->id),
                        Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                )));
                $log->doAdmin('admin');
                Model\Invest::setDetail($invest->id, 'manually-executed', $log->html);
                unset($log);
            }
        }

        // visor de logs
        if ($action == 'viewer') {

            $date = $this->getGet('date') ? $this->getGet('date') : date('Y-m-d');
            $type = in_array($this->getGet('type'), array('log', 'execute', 'daily', 'verify')) ? $this->getGet('type') : 'log';
            if ($type == 'log') {
                $recent = Feed::getLog($date);
                $content = '<pre>'.print_r($recent, 1).'</pre>';
            } elseif ( !empty($type) ) {
                $content = @file_get_contents(GOTEO_LOG_PATH . 'cron/'.str_replace('-', '', $date).'_'.$type.'.log');
                $content = nl2br($content);
            } else {
                return $this->redirect('/admin/accounts/viewer/');
            }

            return array(
                    'folder' => 'accounts',
                    'file' => 'viewer',
                    'content' => $content,
                    'date' => $date,
                    'type' => $type
            );
        }

        if ($action == 'resign' && !empty($id) && $this->getGet('token') == md5('resign')) {
            if ($invest->setResign(true)) {
                Model\Invest::setDetail($invest->id, 'manually-resigned', 'Se ha marcado como donativo independientemente de las recompensas');
                return $this->redirect('/admin/accounts/detail/'.$invest->id);
            } else {
                $errors[] = 'Ha fallado al marcar donativo';
            }
        }

        if (!empty($errors)) {
            Message::error(implode('<br />', $errors));
        }

        // tipos de aporte
        $methods = Model\Invest::methods();
        // estados del proyecto
        $status = Model\Project::status();
        $procStatus = Model\Project::procStatus();
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
            'pool' => 'Monedero virtual',
        );

        // filtros de revisión de proyecto
        $review = array(
            'collect' => 'Recaudado',
            'paypal'  => 'Rev. PayPal',
            'tpv'     => 'Rev. TPV',
            'online'  => 'Pagos Online'
        );

        $issue = array(
            'show' => 'Solamente las incidencias',
            'hide' => 'Ocultar las incidencias'
        );


        /// detalles de una transaccion
        if ($action == 'details') {
            $invest = Model\Invest::get($id);
            $project = Model\Project::get($invest->project);
            $userData = Model\User::get($invest->user);
            return array(
                    'folder' => 'accounts',
                    'file' => 'details',
                    'invest'=>$invest,
                    'project'=>$project,
                    'user'=>$userData,
                    'status'=>$status,
                    'investStatus'=>$investStatus
            );
        }

        // default: action == 'list'
        // listado de aportes
        if ($filters['filtered'] == 'yes') {
            $list = Model\Invest::getList($filters, null, 999);
        } else {
            $list = array();
        }

        $viewData = array(
                'folder' => 'accounts',
                'file' => 'list',
                'list'          => $list,
                'filters'       => $filters,
                'users'         => $users,
                'projects'      => $projects,
                'calls'         => $calls,
                'review'        => $review,
                'methods'       => $methods,
                'types'         => $types,
                'status'        => $status,
                'procStatus'    => $procStatus,
                'issue'         => $issue,
                'investStatus'  => $investStatus
            );

        return $viewData;

    }

}

