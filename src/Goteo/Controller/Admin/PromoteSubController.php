<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Feed,
    Goteo\Application\Message,
	Goteo\Application\Session,
    Goteo\Model;

class PromoteSubController extends AbstractSubController {

static protected $labels = array (
  'list' => 'Listando',
  'details' => 'Detalles del aporte',
  'update' => 'Cambiando el estado al aporte',
  'add' => 'Nuevo Destacado',
  'move' => 'Moviendo a otro Nodo el proyecto',
  'execute' => 'Ejecución del cargo',
  'cancel' => 'Cancelando aporte',
  'report' => 'Informe Financiero del proyecto',
  'viewer' => 'Viendo logs',
  'edit' => 'Editando Destacado',
  'translate' => 'Traduciendo Destacado',
  'reorder' => 'Ordenando los padrinos en Portada',
  'footer' => 'Ordenando las entradas en el Footer',
  'projects' => 'Gestionando proyectos de la convocatoria',
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
);


static protected $label = 'Proyectos destacados';


    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->filters, $subaction));
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
