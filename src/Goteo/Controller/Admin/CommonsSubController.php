<?php
/**
 * Gestion de retornos colectivos
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Lang,
    Goteo\Application\Message,
    Goteo\Application\Session,
	Goteo\Application\Config,
	Goteo\Library\Feed,
    Goteo\Model;

class CommonsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'add' => 'Nuevo retorno',
      'report' => 'Informe de proyecto',
      'edit' => 'Editando retorno',
      'view' => 'Gestión de retornos',
      'info' => 'Información de contacto',
    );


    static protected $label = 'Retornos colectivos';


    protected $filters = array (
      'project' => '',
      'status' => '',
      'icon' => '',
      'projStatus' => '',
    );

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // TODO: permission granularity
        // HARDCODED user 'contratos'
        if($user->id === 'contratos') return true;

        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }


    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }

    public function deleteAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('delete', $id, $this->getFilters(), $subaction));
    }


    public function infoAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('info', $id, $this->getFilters(), $subaction));
    }

    public function fulfillAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('fulfill', $id, $this->getFilters(), $subaction));
    }


    public function viewAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('view', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    /*
     * Usa 'ultra-secret-ws' para procesar las operaciones de cumplir/descumplir y cambiar url
     * ya no usa acciones de fulfill y unfulfill
     */
    public function process ($action = 'list', $id = null, $filters = array()) {
        $lang = Lang::current();
        // variables comunes
        $status = array(
            'nok' => 'Pendiente',
            'ok'  => 'Cumplido'

        );
        $icons = Model\Icon::getAll('social');
        foreach ($icons as $key => $icon) {
            $icons[$key] = $icon->name;
        }
        $licenses = Model\License::getList();
        $statuses = Model\Project::status();
        $projStatus = array(4=>$statuses[4], 5=>$statuses[5]);

        // Acciones sobre proyecto
        if (!empty($id)) {

            // datos del proyecto
            $project = Model\Project::getMini($id);

            switch ($action) {
                case 'info':
                    // ver toda la información de contacto para un proyecto
                    $contact = Model\Project::getContact($id);

                    return array(
                            'folder' => 'commons',
                            'file' => 'info',
                            'project'=>$project,
                            'contact' => $contact,
                            'status' => $statuses
                    );

                    break;

                case 'fulfill':
                    // marcar que el proyecto ha cumplido con los retornos colectivos
                    $errors = array();
                    if ($project->satisfied($errors)) {
                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($project->id);
                        $log->populate('Cambio estado de un proyecto desde retornos colectivos', '/admin/projects',
                            \vsprintf('El admin/revisor %s ha pasado el proyecto %s al estado <span class="red">Retorno cumplido</span>', array(
                                Feed::item('user', $this->user->name, $this->user->id),
                                Feed::item('project', $project->name, $project->id)
                            )));
                        $log->doAdmin('admin');

                    } else {
                        Message::error(implode('<br />', $errors));
                    }

                    return $this->redirect('/admin/commons');
                    break;

                case 'view':
                    // ver los retornos de un proyecto
                    $project->social_rewards = Model\Project\Reward::getAll($project->id, 'social', $lang);

                    return array(
                            'folder' => 'commons',
                            'file' => 'view',
                            'project'=>$project,
                            'statuses' => $statuses,
                            'status' => $status,
                            'icons' => $icons,
                            'licenses' => $licenses,
                            'path' => '/admin/commons'
                    );
                    break;

                // acciones sobre retorno
                case 'add':
                case 'edit':
                    // editar un retorno colectivo
                    if (!$this->getGet('reward_id')) {
                        $reward = new Model\Project\Reward;
                        $reward->id = '';
                        $reward->project = $id;
                        $reward->bonus = 1;
                    } else {
                        $reward = Model\Project\Reward::get($this->getGet('reward_id'));
                    }

                    $stypes = Model\Project\Reward::icons('social');

                    // si llega post -> procesamos el formulario
                    if ($this->hasPost('social_reward-' . $reward->id . '-reward')) {
                        $errors = array();

                        $reward->reward = $this->getPost('social_reward-' . $reward->id . '-reward');
                        $reward->description = $this->getPost('social_reward-' . $reward->id . '-description');
                        $reward->icon = $this->getPost('social_reward-' . $reward->id . '-icon');
                        if ($reward->icon == 'other') {
                            $reward->other = $this->getPost('social_reward-' . $reward->id . '-other');
                        }
                        $reward->license = $this->getPost('social_reward-' . $reward->id . '-' . $reward->icon . '-license');
                        $reward->icon_name = $icons[$reward->icon];

                        if ($reward->save($errors)) {
                            return $this->redirect('/admin/commons/view/'.$id);
                        } else {
                            Message::error(implode('<br />', $errors));
                        }
                    }



                    return array(
                            'folder' => 'commons',
                            'file' => 'edit',
                            'action' => 'edit',
                            'project' => $project,
                            'reward' => $reward,
                            'statuses' => $statuses,
                            'status' => $status,
                            'stypes' => $stypes,
                            'icons' => $icons,
                            'licenses' => $licenses,
                            'path' => '/admin/commons'
                    );
                    break;

                case 'delete':
                    // eliminar retorno
                    if ($this->hasGet('reward_id')) {
                        $errors = array();
                        $reward = Model\Project\Reward::get($this->getGet('reward_id'));

                        if(!$reward->remove($errors)) {
                            Message::error(implode('<br />', $errors));
                        }
                    }
                    return $this->redirect('/admin/commons/view/'.$id);
                    break;
            }

        }


        if (!empty($filters['projStatus'])) {
            // TODO: change with getList, remove getMiniList
            $projects = Model\Project::getMiniList(array('status'=>$filters['projStatus'], 'proj_name'=>$filters['project'], 'order'=>'success'), $this->node);
        } else {
            $projects = Model\Project::getMiniList(array('multistatus'=>"4,5", 'proj_name'=>$filters['project'], 'order'=>'success'), $this->node);
        }

        foreach ($projects as $kay=>&$project) {
            $project->social_rewards = Model\Project\Reward::getAll($project->id, 'social', $lang);
            $cumplidos = 0;
            foreach ($project->social_rewards as $ret) {
                if ($ret->fulsocial) {
                    $cumplidos++;
                }
            }
            $project->cumplidos = $cumplidos;
        }

        return array(
                'folder' => 'commons',
                'file' => 'list',
                'projects'=>$projects,
                'filters' => $filters,
                'statuses' => $statuses,
                'projStatus' => $projStatus,
                'status' => $status,
                'icons' => $icons,
                'autocomplete' => true
        );

    }

}

