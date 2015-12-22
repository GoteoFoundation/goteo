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
 * Gestion de meritocracia
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Message;
use Goteo\Application\Config,
	Goteo\Application\Session,
	Goteo\Library\Feed,
    Goteo\Library\Worth as WorthLib;

class WorthSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'worth-lb-list',
      'edit' => 'worth-lb-edit',
    );


    static protected $label = 'worth-lb';


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


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
                        Feed::item('user', $this->user->name, $this->user->id),
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
