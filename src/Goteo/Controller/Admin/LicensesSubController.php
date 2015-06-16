<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Application\Session,
	Goteo\Library\Feed,
    Goteo\Model;

class LicensesSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null, $filters = array()) {

        // agrupaciones de mas a menos abertas
        $groups = Model\License::groups();

        // tipos de retorno para asociar
        $icons = Model\Icon::getAll('social');


        $errors = array();

        if ($this->isPost()) {

            // objeto
            $license = new Model\License(array(
                'id' => $this->getPost('id'),
                'name' => $this->getPost('name'),
                'description' => $this->getPost('description'),
                'url' => $this->getPost('url'),
                'group' => $this->getPost('group'),
                'order' => $this->getPost('order'),
                'icons' => $this->getPost('icons')
            ));

			if ($license->save($errors)) {
                switch ($this->getPost('action')) {
                    case 'add':
                        Message::info('Licencia añadida correctamente');
                        break;
                    case 'edit':
                        Message::info('Licencia editada correctamente');

                        // Evento Feed
                        $log = new Feed();
                        $log->populate('modificacion de licencia (admin)', '/admin/licenses',
                            \vsprintf("El admin %s ha %s la licencia %s", array(
                                Feed::item('user', Session::getUser()->name, Session::getUserId()),
                                Feed::item('relevant', 'Modificado'),
                                Feed::item('project', $license->name)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                        break;
                }

                // tratar si han marcado pendiente de traducir
                if ($this->getPost('pending') == 1 && !Model\License::setPending($license->id, 'post')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

            }
			else {
                Message::error(implode('<br />', $errors));

                return array(
                        'folder' => 'licenses',
                        'file' => 'edit',
                        'action'  => $this->getPost('action'),
                        'license' => $license,
                        'icons'   => $icons,
                        'groups'  => $groups
                );
			}
		}

        switch ($action) {
            case 'up':
                Model\License::up($id);
                break;
            case 'down':
                Model\License::down($id);
                break;
            case 'add':
                $next = Model\License::next();

                return array(
                        'folder' => 'licenses',
                        'file' => 'edit',
                        'action' => 'add',
                        'license' => (object) array('order' => $next, 'icons' => array()),
                        'icons' => $icons,
                        'groups' => $groups
                );
                break;
            case 'edit':
                $license = Model\License::get($id);

                return array(
                        'folder' => 'licenses',
                        'file' => 'edit',
                        'action' => 'edit',
                        'license' => $license,
                        'icons' => $icons,
                        'groups' => $groups
                );
                break;
            case 'remove':
//                Model\License::delete($id);
                break;
        }

        $licenses = Model\License::getAll($filters['icon'], $filters['group']);

        return array(
                'folder' => 'licenses',
                'file' => 'list',
                'licenses' => $licenses,
                'filters'  => $filters,
                'groups' => $groups,
                'icons'    => $icons
        );

    }

}

