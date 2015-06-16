<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Application\Session,
	Goteo\Library\Feed,
    Goteo\Library\Worth as WorthLib;

class WorthSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null) {

        $errors = array();

        if ($this->isPost() && $action == 'edit') {

            // instancia
            $data = array(
                'id' => $this->getPost('id'),
                'name' => $this->getPost('name'),
                'amount' => $this->getPost('amount')
            );

			if (WorthLib::save($data, $errors)) {
                $action = 'list';
                Message::info('Nivel de meritocracia modificado');

                // Evento Feed
                $log = new Feed();
                $log->populate('modificacion de meritocracia (admin)', '/admin/worth',
                    \vsprintf("El admin %s ha %s el nivel de meritocrácia %s", array(
                        Feed::item('user', Session::getUser()->name, Session::getUserId()),
                        Feed::item('relevant', 'Modificado'),
                        Feed::item('project', $data->name)
                )));
                $log->doAdmin('admin');
                unset($log);

                // tratar si han marcado pendiente de traducir
                if ($this->getPost('pending') == 1 && !\Goteo\Core\Model::setPending($data->id, 'worth')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

            }
			else {
                Message::error(implode('<br />', $errors));

                return array(
                        'folder' => 'worth',
                        'file' => 'edit',
                        'action' => 'edit',
                        'worth' => (object) $data
                );
			}
		}

        switch ($action) {
            case 'edit':
                $worth = WorthLib::getAdmin($id);

                return array(
                        'folder' => 'worth',
                        'file' => 'edit',
                        'action' => 'edit',
                        'worth' => $worth
                );
                break;
        }

        $worthcracy = WorthLib::getAll();

        return array(
                'folder' => 'worth',
                'file' => 'list',
                'worthcracy' => $worthcracy
        );

    }

}
