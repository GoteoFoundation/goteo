<?php
/**
 * Seleccion de proyectos destacados
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Exception;
use Goteo\Library\Feed,
    Goteo\Application\Message,
	Goteo\Application\Session,
    Goteo\Model;

class PromoteSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'add' => 'Nuevo Destacado',
      'edit' => 'Editando Destacado',
      'translate' => 'Traduciendo Destacado',
    );


    static protected $label = 'Proyectos destacados';


    public function editAction($id = null, $subaction = null) {
        $promo = Model\Promote::get($id);
        if ($promo && $this->isPost()) {

            try {
                $project = Model\Project::get($this->getPost('item'));
                if(!$this->isSuperAdmin() && $project->node !== $this->node) {
                    throw new Exception\ModelException('Project out of allowed channel');
                }
                $promo->project = $project->id;
                $promo->active = $this->getPost('active');

                if($this->isMasterNode()) {
                    $promo->title       = $this->getPost('title');
                    $promo->description = $this->getPost('description');
                }
                if ($promo->save($errors)) {
                    // tratar si han marcado pendiente de traducir
                    if($this->isMasterNode() && $this->getPost('pending') == 1 && !Model\Promote::setPending($id, 'post')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                    return $this->redirect();
                }
                else {

                    Message::error(implode(', ', $errors));
                }
            } catch(Exception\ModelException $e) {
                Message::error('Project [' . $this->getPost('item') . '] not found: ' . $e->getMessage());
            }
        }
        return array(
                'template' => 'admin/promote/edit',
                'action' => '/admin/promote/edit/' . $promo->id,
                'promo' => $promo,
                'projects' => Model\Project::published([], $this->node),
                'titleAndDesc' => $this->isMasterNode()
        );
    }


    public function addAction($id = null, $subaction = null) {
        // siguiente orden
        $next = Model\Promote::next($this->node);
        if ($this->isPost()) {

            try {
                $project = Model\Project::get($this->getPost('item'));
                if(!$this->isSuperAdmin() && $project->node !== $this->node) {
                    throw new Exception\ModelException('Project out of allowed channel');
                }

                $data = array(
                    'node' => $this->node,
                    'project' => $project->id,
                    'order' => $this->getPost('order'),
                    'active' => $this->getPost('active')
                );
                if($this->isMasterNode()) {
                    $data['title']       = $this->getPost('title');
                    $data['description'] = $this->getPost('description');
                }
                // objeto
                $promo = new Model\Promote($data);

                if ($promo->save($errors)) {

                    if ($this->isMasterNode()) {
                        // tratar si han marcado pendiente de traducir
                        if($this->getPost('pending') == 1 && !Model\Promote::setPending($id, 'post')) {
                            Message::error('NO se ha marcado como pendiente de traducir!');
                        }
                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($project->id);
                        $log->populate('nuevo proyecto destacado en portada (admin)', self::getUrl(),
                            \vsprintf('El admin %s ha %s el proyecto %s', array(
                                Feed::item('user', $this->user->name, $this->user->id),
                                Feed::item('relevant', 'Destacado en portada', '/'),
                                Feed::item('project', $project->name, $project->id)
                        )));
                        $log->doAdmin('admin');
                        unset($log);
                    }

                    return $this->redirect();
                }
                else {
                    Message::error(implode(', ', $errors));
                }
            } catch(Exception\ModelException $e) {
                Message::error('Project [' . $this->getPost('item') . '] not found: ' . $e->getMessage());
            }
        }
        return array(
                'template' => 'admin/promote/edit',
                'action' => '/admin/promote/add',
                'promo' => (object) array('order' => $next),
                'projects' => Model\Project::published([], $this->node),
                'titleAndDesc' => $this->isMasterNode()
        );
    }


    public function listAction($id = null, $subaction = null) {

        $promoted = Model\Promote::getList(false, $this->node);
        // estados de proyectos
        $status = Model\Project::status();

        return array(
                'template' => 'admin/promote/list',
                'promoted' => $promoted,
                'status' => $status
        );
    }

    public function upAction($id = null, $subaction = null) {
        Model\Promote::up($id, $this->node);
        return $this->redirect();
    }

    public function downAction($id = null, $subaction = null) {
        Model\Promote::down($id, $this->node);
        return $this->redirect();
    }

    public function activeAction($id = null, $subaction = null) {
        Model\Promote::setActive($id, $subaction == 'on' ? true : false);
        return $this->redirect();
    }

    public function removeAction($id = null, $subaction = null) {
        if (Model\Promote::delete($id)) {
            Message::info('Destacado quitado correctamente');
        } else {
            Message::error('No se ha podido quitar el destacado');
        }
        return $this->redirect();

    }



}
