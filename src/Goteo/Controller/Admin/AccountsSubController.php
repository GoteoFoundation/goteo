<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
/**
 * Gestion completa de aportes para nodo central solo
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Tpv;
use Goteo\Library\Paypal;
use Goteo\Library\Feed;
use Goteo\Library\Text;
use Goteo\Library\Currency;
use Goteo\Application\AppEvents;
use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\Exception\ModelNotFountException;
use Goteo\Application\Event\FilterInvestRefundEvent;
use Goteo\Util\Omnipay\Message\EmptySuccessfulResponse;
use Goteo\Payment\Payment;
use Goteo\Model\Invest;
use Goteo\Model\Invest\InvestLocation;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model;

use Omnipay\Common\Message\ResponseInterface;

class AccountsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'accounts-lb-list',
      'details' => 'accounts-lb-details',
      'update' => 'accounts-lb-update',
      'add' => 'accounts-lb-add',
      'move' => 'accounts-lb-move',
      'execute' => 'accounts-lb-execute',
      'cancel' => 'accounts-lb-cancel',
      'report' => 'accounts-lb-report',
      'viewer' => 'accounts-lb-viewer',
      'cancel-pool' => 'accounts-lb-cancel-pool'
    );


    static protected $label = 'accounts-lb';


    protected $filters = array (
      'id' => '',
      'methods' => '',
      'status' => 'all',
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
    static public function isAllowed(User $user, $node) {
        // Only central node allowed here
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    // visor de logs
    public function viewerAction() {

        $date = $this->getGet('date') ? $this->getGet('date') : date('Y-m-d');
        $type = in_array($this->getGet('type'), array('log', 'execute', 'daily', 'verify')) ? $this->getGet('type') : 'log';
        if ($type == 'log') {
            $recent = Feed::getLog($date);
            $content = '<pre>'.print_r($recent, 1).'</pre>';
        } elseif ( !empty($type) ) {
            // TODO: does not work anymore
            $content = @file_get_contents(GOTEO_LOG_PATH . 'cron/'.str_replace('-', '', $date).'_'.$type.'.log');
            $content = nl2br($content);
        } else {
            return $this->redirect('/admin/accounts/viewer');
        }

        return array(
                'template' => 'admin/accounts/viewer',
                'content' => $content,
                'date' => $date,
                'type' => $type
        );
    }


    // Informe de la financiación de un proyecto
    public function reportAction($id) {
        $project = Project::get($id);
        if (!$project instanceof Project) {
            Message::error('Instancia de proyecto no valida');
            return $this->redirect('/admin/accounts');
        }
        $invests = Invest::getAll($id);

        $project->investors = Invest::investors($id, false, true);
        $projectStatus = Project::status();
        $investStatus = Invest::status();

        // Datos para el informe de transacciones correctas
        $Data = Invest::getReportData($project->id, $project->status, $project->round, $project->passed);
        $account = Project\Account::get($project->id);

        return array(
                'template' => 'admin/accounts/report',
                'invests' => $invests,
                'project' => $project,
                'account' => $account,
                'projectStatus' => $projectStatus,
                'status' => $investStatus,
                'Data' => $Data,
                'methods' => Invest::methods()
        );
    }


    /**
     * Contacts to the payment gateway and do the refund process
     * @param  Invest  $invest   [description]
     * @param  boolean $returned [description]
     * @return [type]            [description]
     */
    private function cancelInvest(Invest $invest) {
        $project = $invest->getProject();
        // Put the investion as returned only if it is an unfunded project, cancelled otherwise
        $returned = ($project->status == Project::STATUS_UNFUNDED);
        $ok = false;

        // TODO:
        // @deprecated To be removed once no more preaprovals left
        if ($invest->method == 'paypal' && $invest->preapproval) {
            $verbo = $returned ? 'retornado' : 'cancelado';
            $infinitivo = $returned ? 'retornar' : 'cancelar';
            if (Paypal::cancelPreapproval($invest, $err, $returned)) {
                Message::info("Preaproval paypal $verbo.");
                $log_text = "El admin %s ha $verbo aporte y preapproval de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                $ok = true;

                // mantenimiento de registros relacionados (usuario, proyecto, ...)
                $invest->keepUpdated();

            } else {
                $txt_errors = implode('; ', $err);
                Message::error("Fallo al $infinitivo el preapproval en paypal: " . $txt_errors);
                $log_text = "El admin %s ha fallado al $infinitivo el aporte de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                if ($invest->cancel($returned)) {
                    Message::error("Aporte $verbo");
                } else{
                    Message::error("Fallo al $infinitivo el aporte");
                }
            }
        }
        else {
            try {
                // Omnipay refund()
                $method = $invest->getMethod();
                // print_r($method);die;
                // process gateway refund
                // go to the gateway, gets the response
                $response = $method->refund();

                // Checks and redirects
                if (!$response instanceof ResponseInterface) {
                    throw new \RuntimeException('This response does not implements ResponseInterface');
                }

                // On-sites can return a successful response here
                if ($response->isSuccessful()) {
                    // Event invest success event
                    $invest = $this->dispatch($returned ? AppEvents::INVEST_RETURNED : AppEvents::INVEST_CANCELLED, new FilterInvestRefundEvent($invest, $method, $response))->getInvest();
                    // New Invest Refund Event
                    if( ($invest->method == 'pool' && $invest->status === Invest::STATUS_TO_POOL)
                        || $invest->status === ($returned ? Invest::STATUS_RETURNED : Invest::STATUS_CANCELLED)
                      ) {
                        $ok = true;
                        // Evento Feed
                        $coin = Currency::getDefault('html');
                        $log = new Feed();
                        $log->setTarget($project->id)
                            ->populate(
                                Text::sys('feed-admin-invest-' . ($returned ? 'returned' : 'cancelled') . '-subject'),
                                '/admin/accounts',
                                Text::sys('feed-admin-invest-' . ($returned ? 'returned' : 'cancelled'), [
                                    '%ADMIN%' => Feed::item('user', $this->user->name, $this->user->id),
                                    '%USER%' => Feed::item('user', $invest->getUser()->name, $invest->getUser()->id),
                                    '%AMOUNT%' => Feed::item('money', $invest->amount.' '.$coin),
                                    '%METHOD%' => strtoupper($invest->method),
                                    '%INVEST%' => Feed::item('system', $invest->id),
                                    '%PROJECT%' => Feed::item('project', $invest->getProject()->name, $invest->getProject()->id),
                                    '%DATE%' => Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                                ]))
                            ->doAdmin('admin');
                            Invest::setDetail($invest->id, 'manually-refund', $log->html);
                    } else {
                        Message::error('Error cancelling invest. INVEST:' . $invest->id . ' STATUS: ' . $invest->status);
                    }
                }
                else {
                    $invest = $this->dispatch($returned ? AppEvents::INVEST_RETURN_FAILED : AppEvents::INVEST_CANCEL_FAILED, new FilterInvestRefundEvent($invest, $method, $response))->getInvest();
                    Message::error('Error refunding invest: [' . $response->getMessage().']');
                }
            } catch(\Exception $e) {
                Message::error($e->getMessage());
            }
        }

        return $ok;
    }


    /**
     * Refunds to invest to the original user
     * If project is failed, status will be returned
     * If project is success or active, status will be cancelled
     * @param  Integer $id Invest ID
     */
    public function refunduserAction($id) {
        $invest = Invest::get($id);
        if (!$invest instanceof Invest) {
            Message::error(Text::get('admin-account-invalid-invest', $id));
            return $this->redirect('/admin/accounts');
        }

        $status = $invest->status;
        $coin = Currency::getDefault('html');
        if (!in_array($status, [Invest::STATUS_PENDING, Invest::STATUS_CHARGED, Invest::STATUS_TO_POOL])) {
            Message::error(Text::get('admin-account-invest-non-user-refundable'));
        } else {
            // check the pool for this statuses
            $errors = array();
            $amount = null;
            $current_pool = $invest->pool;
            // If it's already on the user's pool, we will try to refunded anyway
            if($status == Invest::STATUS_TO_POOL) {
                $amount = $invest->getUser()->getPool()->getAmount();
                if($amount < $invest->amount) {
                    Message::error(Text::get('admin-account-invest-user-refund-fail-pool-amount', "$amount $coin"));
                    return $this->redirect('/admin/accounts/details/' . $id);
                }
                // Mark this invest as if the users has choosen not to use the pool on fail
                if($invest->method != 'pool') $invest->setPoolOnFail(false);
            }

            // Cancels the invest, discounts pool if needed
            if($this->cancelInvest($invest)) {

                if(is_null($amount)) {
                    Message::info(Text::get('admin-account-invest-user-refund-ok'));
                } else {
                    // Recalculate pool for cancelled or returned invests
                    $invest->getUser()->getPool()->calculate()->save($errors);
                    if($errors) {
                        Message::error(Text::get('admin-account-invest-user-refund-ko', implode("<br>\n", $errors)));
                    }
                    else {
                        Message::info(Text::get('admin-account-invest-user-refund-ok-pool', "{$invest->amount} $coin"));
                    }
                }
            } else {
                $invest->setPoolOnFail($current_pool);
            }
        }

        return $this->redirect('/admin/accounts/details/' . $id);
    }

    // cancelar aporte y incrementar el monedero
    public function refundpoolAction($id) {
        $invest = Invest::get($id);
        if (!$invest instanceof Invest) {
            Message::error(Text::get('admin-account-invalid-invest', $id));
            return $this->redirect('/admin/accounts');
        }
        $project = $invest->getProject();
        $returned = ($project->status == Project::STATUS_UNFUNDED);
        $coin = Currency::getDefault('html');

        if (!in_array($invest->status, [Invest::STATUS_CHARGED])) {
            Message::error(Text::get('admin-account-invest-non-pool-refundable'));
        } else {

            // Mark this invest as if user choosed pool-on-fail
            $invest->setPoolOnFail(true);

            // Event invest success event
            $invest = $this->dispatch($returned ? AppEvents::INVEST_RETURNED : AppEvents::INVEST_CANCELLED, new FilterInvestRefundEvent($invest, $invest->getMethod(), new EmptySuccessfulResponse()))->getInvest();

            if ($invest->isOnPool()) {
                Message::info(Text::get('admin-account-invest-to-pool-ok', "{$invest->amount} $coin"));
                // Evento Feed
                $log = new Feed();
                $log->setTarget($project->id)
                    ->populate(Text::sys('feed-admin-invest-' . ($returned ? 'returned' : 'cancelled') . '-pool-subject'),
                               '/admin/accounts',
                        Text::sys('feed-admin-invest-' . ($returned ? 'returned' : 'cancelled') .'-pool', [
                            '%ADMIN%' => Feed::item('user', $this->user->name, $this->user->id),
                            '%USER%' => Feed::item('user', $invest->getUser()->name, $invest->getUser()->id),
                            '%AMOUNT%' => Feed::item('money', $invest->amount.' &euro;'),
                            '%METHOD%' => strtoupper($invest->method),
                            '%INVEST%' => Feed::item('system', $invest->id),
                            '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                            '%DATE%' => Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                        ]))
                    ->doAdmin('admin');
                Invest::setDetail($invest->id, 'manually-to-pool', $log->html);

            } else{
                Message::error(Text::get('admin-account-invest-to-pool-ko'));
            }

            // mantenimiento de registros relacionados (usuario, proyecto, ...)
            $invest->keepUpdated();
        }

        return $this->redirect('/admin/accounts/details/' . $id);
    }

    // cancelar aporte antes de ejecución, solo aportes no cargados
    public function switchpoolAction($id) {
        $invest = Invest::get($id);
        if (!$invest instanceof Invest) {
            Message::error(Text::get('admin-account-invalid-invest', $id));
            return $this->redirect('/admin/accounts');
        }
        if($invest->isOnPool() && $invest->pool) {
            Message::error(Text::get('admin-account-switch-pool-fail'));
            return $this->redirect('/admin/accounts/details/' . $id);
        }
        if ($invest->switchPoolOnFail($id)) {
            Message::info('Pool cambiado de estado');
        }
        return $this->redirect('/admin/accounts/details/'.$id);
    }


    /**
     * Refunds to invest to the original user
     * If project is failed, status will be returned
     * If project is success or active, status will be cancelled
     * @param  Integer $id Invest ID
     */
    public function changeuserAction($id) {
        $invest = Invest::get($id);
        $u = $this->getGet('user');
        try{
            $user = User::get($u);
        } catch(ModelNotFountException $e) {}

        if (!$invest instanceof Invest || !Session::isModuleAdmin('users')) {
            Message::error('Invest ['.$id.'] not found, user [' . $u .'] not fount or no permissions to complete the action!');
            return $this->redirect('/admin/accounts/details/' . $id);
        }

        $invest->node = $user->node;
        $invest->user = $user->id;
        $invest->address = $invest->getAddress();

        $errors = [];
        if($invest->save($errors)) {
            Message::info(Text::get('admin-account-user-changed-successfully', ['%ID%' => $id, '%NAME%' => $user->name]));
        } else {
            Message::error(Text::get('admin-account-user-changed-error', ['%ID%' => $id, '%NAME%' => $user->name]) . "<br>" . implode(", ", $errors));
        }

        return $this->redirect('/admin/accounts/details/' . $id);
    }

    public function executeAction($id) {
        $invest = Invest::get($id);
        if (!$invest instanceof Invest || $invest->status != Invest::STATUS_PENDING) {
            Message::error('Invest ['.$id.'] not found or wrong status!');
            return $this->redirect();
        }

        $project = $invest->getProject();
        $userData = User::get($invest->user);

        // ejecutar cargo ahora!!, solo aportes no ejecutados
        // si esta pendiente, ejecutar el cargo ahora (como si fuera final de ronda), deja pendiente el pago secundario
        $errors = array();
        $log_text = '';
        switch ($invest->method) {
            case 'paypal':
                // a ver si tiene cuenta paypal
                $projectAccount = Project\Account::get($invest->project);

                if (empty($projectAccount->paypal)) {
                    // Erroraco!
                    Message::error('El proyecto no tiene cuenta paypal!!, ponersela en la seccion Contrato del dashboard del autor');
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
                    Message::info('Cargo paypal correcto');
                    $log_text = "El admin %s ha ejecutado el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                    $invest->status = 1;

                    // si era incidencia la desmarcamos
                    if ($invest->issue) {
                        Invest::unsetIssue($invest->id);
                        Invest::setDetail($invest->id, 'issue-solved', 'La incidencia se ha dado por resuelta al ejecutar el aporte manualmente por el admin ' . $this->user->name);
                    }


                } else {
                    $txt_errors = implode("<br>\n", $errors);
                    Message::error('Fallo al ejecutar cargo paypal: ' . $txt_errors . '<strong>POSIBLE INCIDENCIA NO COMUNICADA Y APORTE NO CANCELADO, HAY QUE TRATARLA MANUALMENTE</strong>');
                    $log_text = "El admin %s ha fallado al ejecutar el cargo a %s por su aporte de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                }
                break;
            case 'tpv':
                // no tiene sentido ejecutar así un aporte tpv que ya está cobrado
                if (Tpv::execute($invest, $errors)) {
                    Message::info('Cargo sermepa correcto');
                    $log_text = "El admin %s ha ejecutado el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s";
                    $invest->status = 1;
                } else {
                    $txt_errors = implode('; ', $errors);
                    Message::error('Fallo al ejecutar cargo sermepa: ' . $txt_errors);
                    $log_text = "El admin %s ha fallado al ejecutar el cargo a %s por su aporte de %s mediante TPV (id: %s) al proyecto %s del dia %s <br />Se han dado los siguientes errores: $txt_errors";
                }
                break;
            case 'cash':
                $invest->setStatus('1');
                Message::error('Aporte al contado, nada que ejecutar.');
                $log_text = "El admin %s ha dado por ejecutado el aporte manual a nombre de %s por la cantidad de %s (id: %s) al proyecto %s del dia %s";
                $invest->status = 1;
                break;
        }

        if ($log_text) {
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
            Invest::setDetail($invest->id, 'manually-executed', $log->html);
        }

        return $this->redirect();
    }


    // aportes manuales, cargamos la lista completa de usuarios, proyectos y campañas
    public function addAction() {
        // listado de proyectos en campaña
        $projects = Project::active(false, true);
        // campañas
        $calls = Model\Call::getAll();


        // generar aporte manual
        // TODO: reformular esto con eventos y metodo cash
        if ($this->isPost()) {

            $userData = User::get($this->getPost('user'));
            $projectData = Project::get($this->getPost('project'));

            $invest = new Invest(
                array(
                    'amount'    => $this->getPost('amount') ? (int) $this->getPost('amount') : null,
                    'currency' => Currency::current(),
                    'currency_rate' => Currency::rate(),
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
            if ($this->getPost('campaign')) {
                $called = Model\Call::getMini($this->getPost('campaign'));

                if ($called instanceof Model\Call) {
                    $invest->called = $called;
                }
            }
            // print_r($invest);print_r($this->getPost());die;
            $errors = array();
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

                Invest::setDetail($invest->id, 'admin-created', 'Este aporte ha sido creado manualmente por el admin ' . $this->user->name);
                Message::info('Aporte manual creado correctamente, seleccionar recompensa y dirección de entrega.');
                return $this->redirect('/admin/rewards/edit/'.$invest->id);
            } else{
                Message::error('Ha fallado algo al crear el aporte manual<br>'. implode(", ", $errors));
            }

        }

         return array(
                'template' => 'admin/accounts/add',
                'autocomplete'  => true,
                'projects'      => $projects,
                'calls'         => $calls
            );
    }


    // cambiando estado del aporte aporte,
    public function updateAction($id) {

        // el aporte original
        $invest = Invest::get($id);
        if (!$invest instanceof Invest) {
            Message::error(Text::get('admin-account-invalid-invest', $id));
            return $this->redirect('/admin/accounts');
        }

        $status = Invest::status();

        $new = $this->hasPost('status') ? $this->getPost('status') : null;

        if ($this->isPost() && $this->hasPost('update')) {

            // si estan desmarcando incidencia
            if ($invest->issue && $this->getPost('resolve') == 1) {
                Invest::unsetIssue($id);
                Invest::setDetail($id, 'issue-solved', 'La incidencia se ha dado por resuelta por el usuario ' . $this->user->name);
                Message::info('La incidencia se ha dado por resuelta');
            }

            if ($new != $invest->status && isset($new) && isset($status[$new])) {
                if (Invest::query("UPDATE invest SET status=:status WHERE id=:id", array(':id' => $id, ':status' => $new))) {
                    Invest::setDetail($id, 'status-change', 'El admin ' . $this->user->name . ' ha cambiado el estado a '.$status[$new]);
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
            'template' => 'admin/accounts/update',
            'invest' => $invest,
            'status' => $status
        );
    }

   // resolviendo incidencias
    public function solveAction($id) {
        // el aporte original
        $invest = Invest::get($id);
        if (!$invest instanceof Invest) {
            Message::error(Text::get('admin-account-invalid-invest', $id));
            return $this->redirect('/admin/accounts');
        }

        $projectData = Project::getMini($invest->project);
        $userData =  User::get($invest->user);

        $errors = array();

        // primero cancelar
        switch ($invest->method) {
            case 'paypal':
                $err = array();
                if (Paypal::cancelPreapproval($invest, $err)) {
                    Message::error('Preaproval paypal cancelado.');
                    $log_text = "El admin %s ha cancelado aporte y preapproval de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s";
                } else {
                    $txt_errors = implode('; ', $err);
                    Message::error('Fallo al cancelar el preapproval en paypal: ' . $txt_errors);
                    $log_text = "El admin %s ha fallado al cancelar el aporte de %s de %s mediante PayPal (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                    if ($invest->cancel()) {
                        Message::error('Aporte cancelado');
                    } else{
                        Message::error('Fallo al cancelar el aporte');
                    }
                }
                break;
            case 'tpv':
                $err = array();
                if (Tpv::cancelPreapproval($invest, $err)) {
                    $txt_errors = implode('; ', $err);
                    Message::error('Aporte cancelado correctamente. ' . $txt_errors);
                    $log_text = "El admin %s ha anulado el cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s";
                } else {
                    $txt_errors = implode('; ', $err);
                    Message::error('Fallo en la operación. ' . $txt_errors);
                    $log_text = "El admin %s ha fallado al solicitar la cancelación del cargo tpv de %s de %s mediante TPV (id: %s) al proyecto %s del dia %s. <br />Se han dado los siguientes errores: $txt_errors";
                }
                break;
            case 'cash':
                if ($invest->cancel()) {
                    $log_text = "El admin %s ha cancelado aporte manual de %s de %s (id: %s) al proyecto %s del dia %s";
                    Message::error('Aporte cancelado');
                } else{
                    $log_text = "El admin %s ha fallado al cancelar el aporte manual de %s de %s (id: %s) al proyecto %s del dia %s. ";
                    Message::error('Fallo al cancelar el aporte');
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

        }
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

    // detalles de una transaccion
    public function detailsAction($id) {
        // estados del proyecto
        $projectStatus = Project::status();
        // estados de aporte
        $investStatus = Invest::status();
        $invest = Invest::get($id);
        $project = $invest->getProject();
        $userData = User::get($invest->user);
        $methods = Invest::methods();
        $poolable = $invest->getMethod()->isPublic() && $invest->status == Invest::STATUS_CHARGED;
        $refundable = $invest->getMethod()->refundable() && in_array($invest->status, [Invest::STATUS_PENDING, Invest::STATUS_CHARGED, Invest::STATUS_TO_POOL]);
        $location = InvestLocation::get($invest);

        return array(
                'template' => 'admin/accounts/details',
                'invest' => $invest,
                'location' => $location,
                'project' => $project,
                'refundable' => $refundable,
                'poolable' => $poolable,
                'user' => $userData,
                'projectStatus' => $projectStatus,
                'investStatus' => $investStatus,
                'methods' => $methods
        );
    }


    public function switchresignAction($id) {
        $invest = Invest::get($id);
        if ($invest) {
            if ($invest->switchResign()) {
                if($invest->resign) {
                    Invest::setDetail($invest->id, 'manually-resigned', 'Se ha marcado como donativo independientemente de las recompensas');
                }
            } else {
                Message::error('Ha fallado al marcar donativo');
            }
            return $this->redirect('/admin/accounts/details/'.$invest->id);
        }

        Message::error('Invest not found or bad request!');
        return $this->redirect();
    }

    public function listAction() {
        // tipos de aporte
        $methods = Invest::methods();
        // estados del proyecto
        $projectStatus = Project::status();
        $procStatus = Project::procStatus();
        // estados de aporte
        $investStatus = Invest::status();
        // listado de proyectos
        // TODO: esto cambiar a getList de proyectos y convocatorias respectivamente
        $projects = Invest::projects();
        // campañas que tienen aportes
        $calls = Invest::calls();

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

        $filters = $this->getFilters();

        // listado de aportes
        $limit = 25;
        $node = null;
        $total = Invest::getList($filters, $node, 0, 0, true);
        $total_money = Invest::getList($filters, $node, 0, 0, 'money');
        $list = Invest::getList($filters, $node, $this->getGet('pag') * $limit, $limit);
// print_r($list);die("$total $total_money");
        $viewData = array(
                'template' => 'admin/accounts/list',
                'list'          => $list,
                'total'         => $total,
                'total_money'   => $total_money,
                'limit'         => $limit,
                'filters'       => $filters,
                'projects'      => $projects,
                'calls'         => $calls,
                'review'        => $review,
                'methods'       => $methods,
                'types'         => $types,
                'projectStatus' => $projectStatus,
                'procStatus'    => $procStatus,
                'issue'         => $issue,
                'status'  => $investStatus
            );

        return $viewData;

    }

}

