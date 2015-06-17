<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Library\Feed,
    Goteo\Library\Mail,
	Goteo\Library\Template,
    Goteo\Model;

class TransnodesSubController extends AbstractSubController {

static protected $labels = array (
  'list' => 'Listando',
  'details' => 'Detalles del aporte',
  'update' => 'Cambiando el estado al aporte',
  'add' => 'Habilitando traducción',
  'move' => 'Moviendo a otro Nodo el proyecto',
  'execute' => 'Ejecución del cargo',
  'cancel' => 'Cancelando aporte',
  'report' => 'Informe',
  'viewer' => 'Viendo logs',
  'edit' => 'Asignando traducción',
  'translate' => 'Traduciendo Texto',
  'reorder' => 'Ordenando los padrinos en Portada',
  'footer' => 'Ordenando las entradas en el Footer',
  'projects' => 'Informe Impulsores',
  'admins' => 'Asignando administradores del Canal',
  'posts' => 'Entradas de blog en la convocatoria',
  'conf' => 'Configuración de campaña del proyecto',
  'dropconf' => 'Gestionando parte económica de la convocatoria',
  'keywords' => 'Palabras clave',
  'view' => 'Apadrinamientos',
  'info' => 'Información de contacto',
  'send' => 'Comunicación enviada',
  'init' => 'Iniciando un nuevo envío',
  'activate' => 'Iniciando envío',
  'detail' => 'Viendo destinatarios',
  'dates' => 'Fechas del proyecto',
  'accounts' => 'Cuentas del proyecto',
  'images' => 'Imágenes del proyecto',
  'assign' => 'Asignando a una Convocatoria el proyecto',
  'open_tags' => 'Asignando una agrupación al proyecto',
  'rebase' => 'Cambiando Id de proyecto',
  'consultants' => 'Cambiando asesor del proyecto',
  'paypal' => 'Informe PayPal',
  'geoloc' => 'Informe usuarios Localizados',
  'calls' => 'Informe Convocatorias',
  'donors' => 'Informe Donantes',
  'top' => 'Top Cofinanciadores',
  'currencies' => 'Actuales ratios de conversión',
  'preview' => 'Previsualizando Historia',
);


static protected $label = 'Traducciones de nodos';


    protected $filters = array (
  'admin' => '',
  'translator' => '',
);


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
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
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
                $translators = Model\User::getAll(array('role'=>'translator'));


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
        $translators = Model\User::getAll(array('role'=>'translator'));

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
