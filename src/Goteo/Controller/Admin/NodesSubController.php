<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Feed,
    Goteo\Application\Message,
    Goteo\Model;

    /**
     * Gestion canales por administradores
     */
class NodesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Nuevo Canal',
      'move' => 'Reubicando el aporte',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe de proyecto',
      'viewer' => 'Viendo logs',
      'edit' => 'Gestionando Canal',
      'translate' => 'Traduciendo Micronoticia',
      'reorder' => 'Ordenando las entradas en Portada',
      'footer' => 'Ordenando las entradas en el Footer',
      'projects' => 'Gestionando proyectos de la convocatoria',
      'admins' => 'Asignando administradores del Canal',
      'posts' => 'Entradas de blog en la convocatoria',
      'conf' => 'Configurando la convocatoria',
      'dropconf' => 'Gestionando parte económica de la convocatoria',
      'keywords' => 'Palabras clave',
      'view' => 'Gestión de retornos',
      'info' => 'Información de contacto',
      'send' => 'Comunicación enviada',
      'init' => 'Iniciando un nuevo envío',
      'activate' => 'Iniciando envío',
      'detail' => 'Viendo destinatarios',
    );


    static protected $label = 'Canales';


        protected $filters = array (
      'status' => '',
      'admin' => '',
      'name' => '',
    );


    public function adminsAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('admins', $id, $this->filters, $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->filters, $subaction));
    }


    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->filters, $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->filters, $subaction));
    }


    public function process($action = 'list', $id = null, $filters = array()) {

        $errors = array();

        if ($this->isPost()) {

            switch ($this->getPost('action')) {
                case 'add':

                    $url = '/channel/' . $this->getPost('id');
                    // objeto
                    $node = new Model\Node(array(
                                'id' => $this->getPost('id'),
                                'name' => $this->getPost('name'),
                                'email' => $this->getPost('email'),
                                'url' => $url,
                                'active' => $this->getPost('active')
                            ));

                    if ($node->create($errors)) {

                            Message::info('Canal creado');
                            $txt_log = 'creado';

                        // Evento feed
                        $log = new Feed();
                        $log->setTarget($node->id, 'node');
                        $log->populate('Canal gestionado desde admin', 'admin/nodes', \vsprintf('El admin %s ha %s el Canal %s', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', $txt_log),
                                    Feed::item('project', $node->name))
                                ));
                        $log->doAdmin('admin');
                        unset($log);

                        if ($this->getPost('action') == 'add') {
                            Message::info('Puedes asignar ahora sus administradores');
                            return $this->redirect('/admin/nodes/admins/' . $node->id);
                        }
                    } else {
                        Message::error('Fallo al crear, revisar los campos');

                        return array(
                            'folder' => 'nodes',
                            'file' => 'add',
                            'action' => 'add'
                        );
                    }
                    break;
                case 'edit':
                    // objeto
                    $node = new Model\Node(array(
                                'id' => $this->getPost('id'),
                                'name' => $this->getPost('name'),
                                'email' => $this->getPost('email'),
                                'url' => $this->getPost('url'),
                                'active' => $this->getPost('active')
                            ));

                    if ($node->save($errors)) {

                            Message::info('Canal actualizado');
                            $txt_log = 'actualizado';

                        // Evento feed
                        $log = new Feed();
                        $log->setTarget($node->id, 'node');
                        $log->populate('Canal gestionado desde admin', 'admin/nodes', \vsprintf('El admin %s ha %s el Canal %s', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', $txt_log),
                                    Feed::item('project', $node->name))
                                ));
                        $log->doAdmin('admin');
                        unset($log);

                        if ($this->getPost('action') == 'add') {
                            Message::info('Puedes asignar ahora sus administradores');
                            return $this->redirect('/admin/nodes/admins/' . $node->id);
                        }
                    } else {
                        Message::error('Fallo al actualizar, revisar los campos');

                        return array(
                                'folder' => 'nodes',
                                'file' => 'edit',
                                'action' => 'edit',
                                'node' => $node
                        );
                    }
                    break;
            }
        }

        switch ($action) {
            case 'add':
                return array(
                    'folder' => 'nodes',
                    'file' => 'add',
                    'action' => 'add',
                    'node' => null
                );
                break;
            case 'edit':
                $node = Model\Node::get($id);

                return array(
                    'folder' => 'nodes',
                    'file' => 'edit',
                    'action' => 'edit',
                    'node' => $node
                );
                break;
            case 'admins':
                $node = Model\Node::get($id);
                $op = $this->getGet('op');
                if ($op && $this->hasGet('user') && in_array($op, array('assign', 'unassign'))) {
                    if ($node->$op($this->getGet('user'))) {
                        // ok
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                }

                $node->admins = Model\Node::getAdmins($node->id);
                $admins = Model\User::getAdmins(true);

                return array(
                    'folder' => 'nodes',
                    'file' => 'admins',
                    'action' => 'admins',
                    'node' => $node,
                    'admins' => $admins
                );
                break;
        }


        $nodes = Model\Node::getAll($filters);
        $status = array(
            'active' => 'Activo',
            'inactive' => 'Inactivo'
        );
        $admins = Model\Node::getAdmins();

        return array(
            'folder' => 'nodes',
            'file' => 'list',
            'filters' => $filters,
            'nodes' => $nodes,
            'status' => $status,
            'admins' => $admins
        );
    }

}

