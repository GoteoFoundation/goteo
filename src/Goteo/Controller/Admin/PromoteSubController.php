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
 * Seleccion de proyectos destacados
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Config;
use Goteo\Application\Exception;
use Goteo\Library\Feed;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Model;

class PromoteSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'promote-lb-list',
      'add' => 'promote-lb-add',
      'edit' => 'promote-lb-edit',
      'translate' => 'promote-lb-translate',
    );


    static protected $label = 'promote-lb';

    private function checkItemPermission($id) {
        if($sponsor = Model\Promote::get($id)) {
            if($sponsor->node === $this->node) return true;
        }
        throw new ControllerAccessDeniedException('You cannot admin this item');
    }

    public function editAction($id) {
        $this->checkItemPermission($id);

        $promo = Model\Promote::get($id, Config::get('lang'));
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

        //If channel allow projects out of campaign
        $filter=$this->isMasterNode() ? [] : ['status' => [
                        Model\Project::STATUS_IN_CAMPAIGN,
                        Model\Project::STATUS_FUNDED,
                        Model\Project::STATUS_FULFILLED]];

        $node=$this->isMasterNode() ? null :  $this->node;

        return array(
                'template' => 'admin/promote/edit',
                'action' => '/admin/promote/edit/' . $promo->id,
                'promo' => $promo,
                'projects' => Model\Project::published($filter, $node, 0, 0),
                'titleAndDesc' => $this->isMasterNode()
        );
    }


    public function addAction() {
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

        //If channel allow projects out of campaign
        $filter=$this->isMasterNode() ? [] : ['status' => [
                        Model\Project::STATUS_IN_CAMPAIGN,
                        Model\Project::STATUS_FUNDED,
                        Model\Project::STATUS_FULFILLED]];

        $node=$this->isMasterNode() ? null :  $this->node;

        return array(
                'template' => 'admin/promote/edit',
                'action' => '/admin/promote/add',
                'promo' => (object) array('order' => $next),
                'projects' => Model\Project::published($filter, $node, 0, 0),
                'titleAndDesc' => $this->isMasterNode()
        );
    }


    public function listAction() {

        $promoted = Model\Promote::getList(false, $this->node);
        // estados de proyectos
        $status = Model\Project::status();

        return array(
                'template' => 'admin/promote/list',
                'promoted' => $promoted,
                'status' => $status,
                'translator' => $this->isTranslator()
        );
    }

    public function upAction($id) {
        $this->checkItemPermission($id);
        Model\Promote::up($id, $this->node);
        return $this->redirect();
    }

    public function downAction($id) {
        $this->checkItemPermission($id);
        Model\Promote::down($id, $this->node);
        return $this->redirect();
    }

    public function activeAction($id, $subaction = null) {
        $this->checkItemPermission($id);
        Model\Promote::setActive($id, $subaction == 'on' ? true : false);
        return $this->redirect();
    }

    public function removeAction($id = null) {
        $this->checkItemPermission($id);
        if (Model\Promote::delete($id)) {
            Message::info('Destacado quitado correctamente');
        } else {
            Message::error('No se ha podido quitar el destacado');
        }
        return $this->redirect();

    }



}
