<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
    Goteo\Model;

class CriteriaSubController extends AbstractSubController {

static protected $labels = array (
  'list' => 'Listando',
  'details' => 'Detalles del aporte',
  'update' => 'Cambiando el estado al aporte',
  'add' => 'Nuevo Criterio',
  'move' => 'Reubicando el aporte',
  'execute' => 'Ejecución del cargo',
  'cancel' => 'Cancelando aporte',
  'report' => 'Informe de proyecto',
  'viewer' => 'Viendo logs',
  'edit' => 'Editando Criterio',
  'translate' => 'Traduciendo Criterio',
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


static protected $label = 'Criterios de revisión';


    protected $filters = array (
  'section' => 'project',
);


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


    public function process ($action = 'list', $id = null, $filters = array()) {

        $sections = Model\Criteria::sections();

        if (!isset($sections[$filters['section']])) {
            unset($filters['section']);
        }

        $errors = array();

        if ($this->isPost()) {

            // instancia
            $criteria = new Model\Criteria(array(
                'id' => $this->getPost('id'),
                'section' => $this->getPost('section'),
                'title' => $this->getPost('title'),
                'description' => $this->getPost('description'),
                'order' => $this->getPost('order'),
                'move' => $this->getPost('move')
            ));

			if ($criteria->save($errors)) {
                switch ($this->getPost('action')) {
                    case 'add':
                        Message::info('Criterio añadido correctamente');
                        break;
                    case 'edit':
                        Message::info('Criterio editado correctamente');
                        break;
                }

                // tratar si han marcado pendiente de traducir
                if ($this->getPost('pending') == 1 && !Model\Criteria::setPending($criteria->id, 'post')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

            } else {
                Message::error(implode('<br />', $errors));

                return array(
                        'folder' => 'criteria',
                        'file' => 'edit',
                        'action' => $this->getPost('action'),
                        'criteria' => $criteria,
                        'sections' => $sections
                );
			}
		}


        switch ($action) {
            case 'up':
                Model\Criteria::up($id);
                break;
            case 'down':
                Model\Criteria::down($id);
                break;
            case 'add':
                $next = Model\Criteria::next($filters['section']);

                return array(
                        'folder' => 'criteria',
                        'file' => 'edit',
                        'action' => 'add',
                        'criteria' => (object) array('section' => $filters['section'], 'order' => $next, 'cuantos' => $next),
                        'sections' => $sections
                );
                break;
            case 'edit':
                $criteria = Model\Criteria::get($id);

                $cuantos = Model\Criteria::next($criteria->section);
                $criteria->cuantos = ($cuantos -1);

                return array(
                        'folder' => 'criteria',
                        'file' => 'edit',
                        'action' => 'edit',
                        'criteria' => $criteria,
                        'sections' => $sections
                );
                break;
            case 'remove':
                Model\Criteria::delete($id);
                break;
        }

        $criterias = Model\Criteria::getAll($filters['section']);

        return array(
                'folder' => 'criteria',
                'file' => 'list',
                'criterias' => $criterias,
                'sections' => $sections,
                'filters' => $filters
        );

    }

}

