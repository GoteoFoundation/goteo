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

use Goteo\Library\Paypal;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Application\Currency;
use Goteo\Application\AppEvents;
use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFountException;
use Goteo\Application\Event\FilterInvestInitEvent;
use Goteo\Application\Event\FilterInvestRefundEvent;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Application\Event\FilterInvestModifyEvent;
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
                            'feed-admin-invest-' . ($returned ? 'returned' : 'cancelled') . '-subject',
                            '/admin/accounts',
                            new FeedBody(null,null, 'feed-admin-invest-' . ($returned ? 'returned' : 'cancelled'), [
                                '%ADMIN%' => Feed::item('user', $this->user->name, $this->user->id),
                                '%USER%' => Feed::item('user', $invest->getUser()->name, $invest->getUser()->id),
                                '%AMOUNT%' => Feed::item('money', $invest->amount.' '.$coin, $invest->id),
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
            // If it's already on the user's pool, we will try to refund anyway
            if($status == Invest::STATUS_TO_POOL) {
                $amount = $invest->getUser()->getPool()->getAmount();
                if($amount < $invest->amount) {
                    Message::error(Text::get('admin-account-invest-user-refund-fail-pool-amount', "$amount $coin"));
                    return $this->redirect('/admin/accounts/details/' . $id);
                }
            }

            // Mark this invest as if the users has choosen not to use the pool on fail
            if($invest->method != 'pool') $invest->setPoolOnFail(false);

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
                    ->populate('feed-admin-invest-' . ($returned ? 'returned' : 'cancelled') . '-pool-subject',
                               '/admin/accounts',
                        new FeedBody(null,null,'feed-admin-invest-' . ($returned ? 'returned' : 'cancelled') .'-pool', [
                            '%ADMIN%' => Feed::item('user', $this->user->name, $this->user->id),
                            '%USER%' => Feed::item('user', $invest->getUser()->name, $invest->getUser()->id),
                            '%AMOUNT%' => Feed::item('money', $invest->amount.' &euro;', $invest->id),
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

    public function converttopoolAction($id) {
        $invest = Invest::get($id);
        if (!$invest instanceof Invest) {
            Message::error(Text::get('admin-account-invalid-invest', $id));
            return $this->redirect('/admin/accounts');
        }

        $project = $invest->getProject();
        $errors = [];
        if($project instanceOf Project) {
            $invest->status = Invest::STATUS_TO_POOL;
            $invest->pool = true;
            $invest->project = null;
            if($invest->save($errors)) {
                if(!$invest->setRewards([])) {
                    $errors[] = 'Failed to remove rewards from invest';
                }
            }
        } else {
            $errors[] = 'Already a pool invest!';
        }
        if($errors) {
            Message::error(Text::get('admin-account-convert-to-pool-ko', implode(',', $errors)));
        } else {
            Message::info(Text::get('admin-account-convert-to-pool-ok'));
        }
        return $this->redirect('/admin/accounts/details/' . $id);
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

        $event = new FilterInvestModifyEvent($invest);
        $invest = $event->getNewInvest();
        $invest->node = $user->node;
        $invest->user = $user->id;
        $invest->address = $invest->getAddress();

        try {
            $this->dispatch(AppEvents::INVEST_MODIFY, $event);
            Message::info(Text::get('admin-account-user-changed-successfully', ['%ID%' => $id, '%NAME%' => $user->name]));
        } catch(ModelException $e) {
            Message::error(Text::get('admin-account-user-changed-error', ['%ID%' => $id, '%NAME%' => $user->name]) . "<br>" . $e->getMessage());
        }

        return $this->redirect('/admin/accounts/details/' . $id);
    }

    // aportes manuales, cargamos la lista completa de usuarios, proyectos y campañas
    public function addAction() {

        // generar aporte manual
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
                    'anonymous' => (bool)$this->getPost('anonymous'),
                    'resign'    => 1,
                    'admin'     => $this->user->id
                )
            );


            $errors = array();
            if ($invest->save($errors)) {
                // Event invest success

                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id)
                    ->populate('feed-admin-invest-manual-subject',
                    '/admin/accounts',
                    new FeedBody(null, null, 'feed-admin-invest-manual', [
                        '%ADMIN%' => Feed::item('user', $this->user->name, $this->user->id),
                        '%AMOUNT%' => Feed::item('money', $invest->amount.' &euro;', $invest->id),
                        '%PROJECT%' => Feed::item('project', $projectData->name, $projectData->id),
                        '%USER%' => Feed::item('user', $userData->name, $userData->id)
                    ]))
                   ->doAdmin('money');

                Invest::setDetail($invest->id, 'admin-created', 'Invest created manually by the admin ' . $this->user->name);
                Message::info(Text::get('invest-created-manually-ok'));

                // New Invest Init Event
                $method = $this->dispatch(AppEvents::INVEST_INIT, new FilterInvestInitEvent($invest, $invest->getMethod(), $this->request))->getMethod();

                // go to the gateway, gets the response
                $response = $method->purchase();
                // New Invest Request Event
                $response = $this->dispatch(AppEvents::INVEST_INIT_REQUEST, new FilterInvestRequestEvent($method, $response))->getResponse();

                // Checks and redirects
                if (!$response instanceof ResponseInterface) {
                    throw new \RuntimeException('This response does not implements ResponseInterface.');
                }

                // On-sites can return a succesful response here
                if ($response->isSuccessful()) {
                    // Event invest success
                    $filter = new FilterInvestRequestEvent($method, $response);
                    $filter->skipMail(true);
                    $this->dispatch(AppEvents::INVEST_SUCCEEDED, $filter);
                }

                return $this->redirect('/admin/rewards/edit/'.$invest->id);
            } else{
                Message::error(Text::get('invest-created-manually-ko', implode(", ", $errors)));
            }

        }

         return array(
                'template' => 'admin/accounts/add',
                'autocomplete'  => true,
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
        if($this->cancelInvest($invest)) {
            // Recalculate pool for cancelled or returned invests
            $invest->getUser()->getPool()->calculate()->save($errors);
            if($errors) {
                Message::error(Text::get('admin-account-invest-user-refund-ko', implode("<br>\n", $errors)));
            }
            else {
                Message::info(Text::get('admin-account-invest-user-refund-ok-pool', "{$invest->amount} $coin"));
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

            }

        }

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

