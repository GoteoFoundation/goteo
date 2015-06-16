<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Feed,
    Goteo\Application\Message,
	Goteo\Application\Session,
    Goteo\Model;

class PromoteSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

        $errors = array();

        $node = $this->node;

        if ($this->isPost()) {

            if( ! ($el_item = $this->getPost('item') ) ) {
                error_log($el_item);
                $el_item = null;
            }

            // objeto
            $promo = new Model\Promote(array(
                'id' => $id,
                'node' => $node,
                'project' => $el_item,
                'title' => $this->getPost('title'),
                'description' => $this->getPost('description'),
                'order' => $this->getPost('order'),
                'active' => $this->getPost('active')
            ));

			if ($promo->save($errors)) {
                if ($this->getPost('action') == 'add') {
                    $projectData = Model\Project::getMini($el_item);

                    if ($node == \GOTEO_NODE) {
                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($projectData->id);
                        $log->populate('nuevo proyecto destacado en portada (admin)', '/admin/promote',
                            \vsprintf('El admin %s ha %s el proyecto %s', array(
                                Feed::item('user', Session::getUser()->name, Session::getUserId()),
                                Feed::item('relevant', 'Destacado en portada', '/'),
                                Feed::item('project', $projectData->name, $projectData->id)
                        )));
                        $log->doAdmin('admin');
                        unset($log);
                    }
                }

                // tratar si han marcado pendiente de traducir
                if ($this->getPost('pending') == 1 && !Model\Promote::setPending($promo->id, 'post')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

                return $this->redirect('/admin/promote');
			}
			else {

                Message::error(implode(', ', $errors));

                switch ($this->getPost('action')) {
                    case 'add':
                        return array(
                                'folder' => 'promote',
                                'file' => 'edit',
                                'action' => 'add',
                                'promo' => $promo
                        );
                        break;
                    case 'edit':
                        return array(
                                'folder' => 'promote',
                                'file' => 'edit',
                                'action' => 'edit',
                                'promo' => $promo
                        );
                        break;
                }
			}
		}

        switch ($action) {
            case 'active':
                $set = $flag == 'on' ? true : false;
                Model\Promote::setActive($id, $set);
                return $this->redirect('/admin/promote');
                break;
            case 'up':
                Model\Promote::up($id, $node);
                return $this->redirect('/admin/promote');
                break;
            case 'down':
                Model\Promote::down($id, $node);
                return $this->redirect('/admin/promote');
                break;
            case 'remove':
                if (Model\Promote::delete($id)) {
                    Message::info('Destacado quitado correctamente');
                } else {
                    Message::error('No se ha podido quitar el destacado');
                }
                return $this->redirect('/admin/promote');
                break;
            case 'add':
                // siguiente orden
                $next = Model\Promote::next($node);

                return array(
                        'folder' => 'promote',
                        'file' => 'edit',
                        'action' => 'add',
                        'promo' => (object) array('order' => $next, 'node'=>$node),
                        'autocomplete' => true
                );
                break;
            case 'edit':
                $promo = Model\Promote::get($id);

                return array(
                        'folder' => 'promote',
                        'file' => 'edit',
                        'action' => 'edit',
                        'promo' => $promo,
                        'autocomplete' => true
                );
                break;
        }


        $promoted = Model\Promote::getList(false, $node);
        // estados de proyectos
        $status = Model\Project::status();

        return array(
                'folder' => 'promote',
                'file' => 'list',
                'promoted' => $promoted,
                'status' => $status
        );

    }

}
