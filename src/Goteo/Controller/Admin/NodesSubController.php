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
 * Gestion de canales/nodos
 */
namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Library\Feed,
    Goteo\Application\Exception\ModelException,
    Goteo\Application\Message,
    Goteo\Application\Config,
    Goteo\Model;

    /**
     * Gestion canales por administradores
     */
class NodesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'nodes-lb-list',
      'add' => 'nodes-lb-add',
      'edit' => 'nodes-lb-edit',
      'admins' => 'nodes-lb-admins',
    );


    static protected $label = 'nodes-lb';


    protected $filters = array (
          'status' => '',
          'admin' => '',
          'name' => '',
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

    /**
     * Get or exception to handle node
     * TODO: some extra security check?
     * @param  string $id node id
     */
    private function getNode($id) {
        $node = Model\Node::get($id);
        $this->contextVars([
                'node' => $node,
                'node_admins' => Model\Node::getAdmins($id)
            ], '/admin/nodes/');
        return $node;
    }

    public function adminsAction($id = null) {

        $node = $this->getNode($id);

        $op = $this->getGet('op');
        $user = $this->getGet('user');
        if ($op && $user && in_array($op, array('assign', 'unassign'))) {
            if ($node->$op($user)) {
                // ok
            } else {
                Message::error("Error with operation [$op] and user [$user]");
            }
        }

        $node->admins = Model\Node::getAdmins($node->id);
        $admins = Model\Node::getAdmins();

        return array(
            'template' => 'admin/nodes/admins',
            'admins' => $admins
        );
    }


    public function editAction($id) {
        $node = $this->getNode($id);
        if($this->isPost()) {
            $node->name = $this->getPost('name');
            $node->email = $this->getPost('email');
            $node->default_consultant = $this->getPost('default_consultant');

            if(!$node->isMasterNode()) {
                if($this->hasPost('id')) {
                    try {
                        $node->rebase($this->getPost('id'));
                    } catch(ModelException $e) {
                        Message::error('Error changing node id: ' . $e->getMessage());
                    }
                }
                $node->url = $this->getPost('url');
                $node->active = $this->getPost('active');
                $node->sponsors_limit = $this->getPost('sponsors_limit');
            }

            $errors = array();
            if ($node->save($errors)) {

                Message::info('Canal actualizado');
                $txt_log = 'actualizado';

                // Evento feed
                $log = new Feed();
                $log->setTarget($node->id, 'node');
                $log->populate('Canal gestionado desde admin', 'admin/nodes', \vsprintf('El admin %s ha %s el Canal %s', array(
                            Feed::item('user', $this->user->name, $this->user->id),
                            Feed::item('relevant', $txt_log),
                            Feed::item('project', $node->name))
                        ));
                $log->doAdmin('admin');
                return $this->redirect();
            } else {
                Message::error('Fallo al actualizar, revisar los campos. ', implode('<br>', $errors));
            }
        }
        return array( 'template' => 'admin/nodes/edit', 'node_admins' => Model\Node::getAdmins() );
    }


    public function addAction($id = null) {

       if($this->isPost()) {

            // objeto
            $node = new Model\Node(array(
                        'id' => $this->getPost('id'),
                        'name' => $this->getPost('name'),
                        'email' => $this->getPost('email'),
                        'url' => $this->getPost('url'),
                        'active' => $this->getPost('active'),
                        'default_consultant' => $this->getPost('default_consultant'),
                        'sponsors_limit' => $this->getPost('sponsors_limit')
                    ));

            $errors = array();
            if ($node->create($errors)) {

                Message::info('Canal creado');
                $txt_log = 'creado';

                // Evento feed
                $log = new Feed();
                $log->setTarget($node->id, 'node');
                $log->populate('Canal gestionado desde admin', 'admin/nodes', \vsprintf('El admin %s ha %s el Canal %s', array(
                            Feed::item('user', $this->user->name, $this->user->id),
                            Feed::item('relevant', $txt_log),
                            Feed::item('project', $node->name))
                        ));
                $log->doAdmin('admin');

                Message::info('Puedes asignar ahora sus administradores');
                return $this->redirect('/admin/nodes/admins/' . $node->id);

            } else {
                Message::error('Fallo al crear, revisar los campos.' . implode('<br>', $errors));
            }
        }
        return array( 'template' => 'admin/nodes/add', 'node_admins' => Model\Node::getAdmins(), 'node' => $node );
    }


    public function listAction() {
        $filters = $this->getFilters();
        $nodes = Model\Node::getAll($filters);
        $status = array(
            'active' => 'Activo',
            'inactive' => 'Inactivo'
        );
        $admins = Model\Node::getAdmins();

        return array(
            'template' => 'admin/nodes/list',
            'filters' => $filters,
            'nodes' => $nodes,
            'status' => $status,
            'admins' => $admins
        );
    }

}

