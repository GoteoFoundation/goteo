<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Application\Session,
	Goteo\Library\Feed,
    Goteo\Model;

class IconsSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null, $filters = array()) {

        $groups = Model\Icon::groups();

        $errors = array();

        if ($this->isPost()) {

            // instancia
            $icon = new Model\Icon(array(
                'id' => $this->getPost('id'),
                'name' => $this->getPost('name'),
                'description' => $this->getPost('description'),
                'order' => $this->getPost('order'),
                'group' => empty($this->getPost('group')) ? null : $this->getPost('group')
            ));

			if ($icon->save($errors)) {
                switch ($this->getPost('action')) {
                    case 'add':
                        Message::info('Nuevo tipo añadido correctamente');
                        break;
                    case 'edit':
                        Message::info('Tipo editado correctamente');

                        // Evento Feed
                        $log = new Feed();
                        $log->populate('modificacion de tipo de retorno/recompensa (admin)', '/admin/icons',
                            \vsprintf("El admin %s ha %s el tipo de retorno/recompensa %s", array(
                                Feed::item('user', Session::getUser()->name, Session::getUserId()),
                                Feed::item('relevant', 'Modificado'),
                                Feed::item('project', $icon->name)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                        break;
                }

                // tratar si han marcado pendiente de traducir
                if ($this->getPost('pending') == 1 && !Model\Icon::setPending($icon->id, 'post')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

            }
			else {
                Message::error(implode('<br />', $errors));

                return array(
                        'folder' => 'icons',
                        'file' => 'edit',
                        'action' => $this->getPost('action'),
                        'icon' => $icon,
                        'groups' => $groups
                );
			}
		}

        switch ($action) {
            case 'edit':
                $icon = Model\Icon::get($id);

                return array(
                        'folder' => 'icons',
                        'file' => 'edit',
                        'action' => 'edit',
                        'icon' => $icon,
                        'groups' => $groups
                );
                break;
        }

        $icons = Model\Icon::getAll($filters['group']);
        return array(
                'folder' => 'icons',
                'file' => 'list',
                'icons' => $icons,
                'groups' => $groups,
                'filters' => $filters
        );

    }

}
