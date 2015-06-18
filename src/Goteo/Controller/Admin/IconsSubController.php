<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Application\Session,
	Goteo\Library\Feed,
    Goteo\Model;

class IconsSubController extends AbstractSubController {

static protected $labels = array (
  'list' => 'Listando',
  'details' => 'Detalles del aporte',
  'update' => 'Cambiando el estado al aporte',
  'add' => 'Nueva Pregunta',
  'move' => 'Reubicando el aporte',
  'execute' => 'Ejecución del cargo',
  'cancel' => 'Cancelando aporte',
  'report' => 'Informe de proyecto',
  'viewer' => 'Viendo logs',
  'edit' => 'Editando Tipo',
  'translate' => 'Traduciendo Tipo',
  'reorder' => 'Ordenando las entradas en Portada',
  'footer' => 'Ordenando las entradas en el Footer',
  'projects' => 'Gestionando proyectos de la convocatoria',
  'admins' => 'Asignando administradores de la convocatoria',
  'posts' => 'Entradas de blog en la convocatoria',
  'conf' => 'Configurando la convocatoria',
  'dropconf' => 'Gestionando parte económica de la convocatoria',
  'keywords' => 'Palabras clave',
  'view' => 'Gestión de retornos',
  'info' => 'Información de contacto',
);


static protected $label = 'Tipos de Retorno';


    protected $filters = array (
  'group' => '',
);


    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->filters, $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->filters, $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->filters, $subaction));
    }


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
                                Feed::item('user', $this->user->name, $this->user->id),
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
