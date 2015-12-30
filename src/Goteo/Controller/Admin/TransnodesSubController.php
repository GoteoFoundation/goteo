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
 * Traducción de nodos
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Message,
	Goteo\Library\Feed,
	Goteo\Model\Template,
    Goteo\Model;

class TransnodesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Transnodes-lb-list',
      'edit' => 'Transnodes-lb-edit',
    );


    static protected $label = 'Transnodes-lb';


    protected $filters = array (
      'admin' => '',
      'translator' => '',
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

    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
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

        $errors  = array();

        switch ($action) {
            case 'edit':
            case 'assign':
            case 'unassign':

                // a ver si tenemos nodo
                if (empty($id) && $this->getPost('node')) {
                    $id = $this->getPost('node');
                }

                if (!empty($id)) {
                    $node = Model\Node::getMini($id);
                } else {
                    Message::error('No hay nodo sobre la que operar');
                    return $this->redirect('/admin/transnodes');
                }

                // asignar o desasignar
                // la id de revision llega en $id
                // la id del usuario llega por get
                $user = $this->getGet('user');
                if (!empty($user)) {
                    $userData = Model\User::getMini($user);

                    $assignation = new Model\User\Translate(array(
                        'item' => $node->id,
                        'type' => 'node',
                        'user' => $user
                    ));

                    switch ($action) {
                        case 'assign': // se la ponemos
                            $assignation->save($errors);
                            $what = 'Asignado';
                            break;
                        case 'unassign': // se la quitamos
                            $assignation->remove($errors);
                            $what = 'Desasignado';
                            break;
                    }

                    if (empty($errors)) {
                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($userData->id, 'user');
                        $log->populate($what . ' traduccion de nodo (admin)', '/admin/transnodes',
                            \vsprintf('El admin %s ha %s a %s la traducción del nodo %s', array(
                                Feed::item('user', $this->user->name, $this->user->id),
                                Feed::item('relevant', $what),
                                Feed::item('user', $userData->name, $userData->id),
                                Feed::item('node', $node->name, $node->id)
                        )));
                        $log->doAdmin('admin');
                        unset($log);
                    } else {
                        Message::error(implode('<br />', $errors));
                    }

                    return $this->redirect('/admin/transnodes/edit/'.$node->id);
                }
                // fin asignar o desasignar

                $node->translators = Model\User\Translate::translators($id, 'node');
                $translators = Model\User::getList(array('role'=>'translator'));


                return array(
                        'folder' => 'transnodes',
                        'file'   => 'edit',
                        'action' => $action,
                        'availables' => $availables,
                        'translators' => $translators,
                        'node'=> $node
                );

                break;
        }

        $nodes = Model\Node::getTranslates($filters);
        $admins = Model\Node::getAdmins();
        $translators = Model\User::getList(array('role'=>'translator'));

        return array(
                'folder' => 'transnodes',
                'file' => 'list',
                'nodes' => $nodes,
                'filters' => $filters,
                'fields'  => array('admin', 'translator'),
                'admins' => $admins,
                'translators' => $translators
        );

    }

}
