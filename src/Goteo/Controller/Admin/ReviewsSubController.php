<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Goteo\Library\Feed,
    Goteo\Application\Config,
    Goteo\Application\Message,
	Goteo\Application\Session,
    Goteo\Model;

class ReviewsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'reviews-lb-list',
      'add' => 'reviews-lb-add',
      'edit' => 'reviews-lb-edit',
    );


    static protected $label = 'reviews-lb';


    protected $filters = array (
      'project' => '',
      'status' => 'open',
      'checker' => '',
    );


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(Model\User $user, $node) {
        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function reportAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('report', $id, $this->getFilters(), $subaction));
    }

    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }

    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }

    public function closeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('close', $id, $this->getFilters(), $subaction));
    }

    public function unreadyAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('unready', $id, $this->getFilters(), $subaction));
    }

    public function assignAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('assign', $id, $this->getFilters(), $subaction));
    }

    public function unassignAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('unassign', $id, $this->getFilters(), $subaction));
    }

    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array()) {

        $node = $this->node;

        $errors  = array();

        switch ($action) {
            case 'add':
            case 'edit':

                // el get se hace con el id del proyecto
                $review = Model\Review::get($id, Config::get('lang'));

                $project = Model\Project::getMini($review->project);

                if (empty($id) || ($action == 'edit' && !$review instanceof Model\Review)) {
                    Message::error('Hemos perdido de vista el proyecto o la revisión');
                    return $this->redirect('/admin/reviews');
                }

                if ($this->isPost() && $this->hasPost('save')) {

                    // instancia
                    $review->id         = $this->getPost('id');
                    $review->project    = $this->getPost('project');
                    $review->to_checker = $this->getPost('to_checker');
                    $review->to_owner   = $this->getPost('to_owner');

                    if ($review->save($errors)) {
                        switch ($action) {
                            case 'add':
                                Message::info('Revisión iniciada correctamente');

                                // Evento Feed
                                $log = new Feed();
                                $log->setTarget($project->id);
                                $log->populate('valoración iniciada (admin)', '/admin/reviews',
                                    \vsprintf('El admin %s ha %s la valoración de %s', array(
                                        Feed::item('user', $this->user->name, $this->user->id),
                                        Feed::item('relevant', 'Iniciado'),
                                        Feed::item('project', $project->name, $project->id)
                                )));
                                $log->doAdmin('admin');
                                unset($log);

                                return $this->redirect('/admin/reviews?project='.  urlencode($project->id));
                                break;
                            case 'edit':
                                Message::info('Datos editados correctamente');
                                return $this->redirect('/admin/reviews');
                                break;
                        }
                    } else {
                        Message::error('No se han podido grabar los datos. ', implode(', ', $errors));
                    }
                }

                return array(
                        'folder' => 'reviews',
                        'file'   => 'edit',
                        'action' => $action,
                        'review' => $review,
                        'project'=> $project
                );

                break;
            case 'close':
                // el get se hace con el id del proyecto
                $review = Model\Review::getData($id);

                // marcamos la revision como completamente cerrada
                if (Model\Review::close($id, $errors)) {
                    Message::info('La revisión se ha cerrado');

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($review->project);
                    $log->populate('valoración finalizada (admin)', '/admin/reviews',
                        \vsprintf('El admin %s ha dado por %s la valoración de %s', array(
                            Feed::item('user', $this->user->name, $this->user->id),
                            Feed::item('relevant', 'Finalizada'),
                            Feed::item('project', $review->name, $review->project)
                    )));
                    $log->doAdmin('admin');
                    unset($log);

                } else {
                    Message::error('La revisión no se ha podido cerrar. '.implode(', ', $errors));
                }
                return $this->redirect('/admin/reviews');
                break;
            case 'unready':
                // se la reabrimos para que pueda seguir editando
                // la id de revision llega en $id
                // la id del usuario llega por get
                $user = $this->getGet('user');
                if (!empty($user)) {
                    $user_rev = new Model\User\Review(array(
                        'id' => $id,
                        'user' => $user
                    ));
                    $user_rev->unready($errors);
                    if (!empty($errors)) {
                        Message::error(implode(', ', $errors));
                    }
                }
                return $this->redirect('/admin/reviews');
                break;
            case 'assign':
                // asignamos la revision a este usuario
                // la id de revision llega en $id
                // la id del usuario llega por get
                $user = $this->getGet('user');
                if (!empty($user)) {
                    $assignation = new Model\User\Review(array(
                        'id' => $id,
                        'user' => $user
                    ));
                    if ($assignation->save($errors)) {

                        $userData = Model\User::getMini($user);
                        $reviewData = Model\Review::getData($id);

                        Message::info('Revisión asignada correctamente');

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($userData->id, 'user');
                        $log->populate('asignar revision (admin)', '/admin/reviews',
                            \vsprintf('El admin %s ha %s a %s la revisión de %s', array(
                                Feed::item('user', $this->user->name, $this->user->id),
                                Feed::item('relevant', 'Asignado'),
                                Feed::item('user', $userData->name, $userData->id),
                                Feed::item('project', $reviewData->name, $reviewData->project)
                        )));
                        $log->setTarget($userData->id, 'user');
                        $log->doAdmin('admin');
                        unset($log);

                    } else {
                        Message::error(implode(', ', $errors));
                    }
                }
                return $this->redirect('/admin/reviews');
                break;
            case 'unassign':
                // se la quitamos a este revisor
                // la id de revision llega en $id
                // la id del usuario llega por get
                $user = $this->getGet('user');
                if (!empty($user)) {
                    $assignation = new Model\User\Review(array(
                        'id' => $id,
                        'user' => $user
                    ));
                    if ($assignation->remove($errors)) {

                        $userData = Model\User::getMini($user);
                        $reviewData = Model\Review::getData($id);

                        Message::info('Revisión desasignada correctamente');

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($userData->id, 'user');
                        $log->populate('Desasignar revision (admin)', '/admin/reviews',
                            \vsprintf('El admin %s ha %s a %s la revisión de %s', array(
                                Feed::item('user', $this->user->name, $this->user->id),
                                Feed::item('relevant', 'Desasignado'),
                                Feed::item('user', $userData->name, $userData->id),
                                Feed::item('project', $reviewData->name, $reviewData->project)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                    } else {
                        Message::error(implode(', ', $errors));
                    }
                }
                return $this->redirect('/admin/reviews');
                break;
            case 'report':
                // mostramos los detalles de revision
                // ojo que este id es la id del proyecto, no de la revision
                $review = Model\Review::get($id, Config::get('lang'));
                $review = Model\Review::getData($review->id);

                $evaluation = array();

                foreach ($review->checkers as $user=>$user_data) {
                    $evaluation[$user] = Model\Review::getEvaluation($review->id, $user);
                }


                return array(
                        'folder' => 'reviews',
                        'file' => 'report',
                        'review'     => $review,
                        'evaluation' => $evaluation
                );
                break;
        }

        // si hay proyecto filtrado, no filtramos estado
        if (!empty($filters['project'])) unset($filters['status']);

        $list = Model\Review::getList($filters, $node);
        $projects = Model\Review::getProjects($node);
        $status = array(
            'unstarted' => 'No iniciada',
            'open' => 'Abierta',
            'closed' => 'Cerrada'
        );
        $checkers = Model\User::getList(array('role'=>'checker'));

        return array(
                'folder' => 'reviews',
                'file' => 'list',
                'list' => $list,
                'filters' => $filters,
                'projects' => $projects,
                'status' => $status,
                'checkers' => $checkers
        );

    }

}
