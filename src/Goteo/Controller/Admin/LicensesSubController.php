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
 * Gestion de licencias
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
    Goteo\Application\Session,
	Goteo\Application\Config,
	Goteo\Library\Feed,
    Goteo\Model;

class LicensesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'licenses-lb-list',
      'add' => 'licenses-lb-add',
      'edit' => 'licenses-lb-edit',
      'translate' => 'licenses-lb-translate',
    );


    static protected $label = 'licenses-lb';


    protected $filters = array (
      'group' => '',
      'icon' => '',
    );

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->getFilters(), $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }

    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }

    public function upAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('up', $id, $this->getFilters(), $subaction));
    }

    public function downAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('down', $id, $this->getFilters(), $subaction));
    }

    public function removeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('remove', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


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
                                Feed::item('user', $this->user->name, $this->user->id),
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
                $license = Model\License::get($id, Config::get('lang'));

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

