<?php
/**
 * Gestion de preguntas frecuentes
 */
namespace Goteo\Controller\Admin;

use Goteo\Core\Redirection,
    Goteo\Application\Message,
	Goteo\Application\Config,
    Goteo\Model;

class FaqSubController extends AbstractSubController {

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
      'edit' => 'Editando Pregunta',
      'translate' => 'Traduciendo Pregunta',
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


    static protected $label = 'FAQs';


    protected $filters = array (
      'section' => 'node',
    );

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node allowed here
        if( ! Config::isMasterNode($node) ) return false;
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


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array()) {

        $sections = Model\Faq::sections();

        if (!isset($sections[$filters['section']])) {
            unset($filters['section']);
        }

        $errors = array();

        if ($this->isPost()) {

            // instancia
            $faq = new Model\Faq(array(
                'id' => $this->getPost('id'),
                'node' => $this->node,
                'section' => $this->getPost('section'),
                'title' => $this->getPost('title'),
                'description' => $this->getPost('description'),
                'order' => $this->getPost('order'),
                'move' => $this->getPost('move')
            ));

			if ($faq->save($errors)) {
                switch ($this->getPost('action')) {
                    case 'add':
                        Message::info('Pregunta añadida correctamente');
                        break;
                    case 'edit':
                        Message::info('Pregunta editado correctamente');
                        break;
                }

                // tratar si han marcado pendiente de traducir
                if ($this->getPost('pending') == 1 && !Model\Faq::setPending($faq->id, 'post')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

            } else {
                Message::error(implode('<br />', $errors));

                return array(
                        'folder' => 'faq',
                        'file' => 'edit',
                        'action' => $this->getPost('action'),
                        'faq' => $faq,
                        'filter' => $filter,
                        'sections' => $sections
                );
			}
		}


        switch ($action) {
            case 'up':
                Model\Faq::up($id);
                return $this->redirect('/admin/faq');
                break;
            case 'down':
                Model\Faq::down($id);
                return $this->redirect('/admin/faq');
                break;
            case 'add':
                $next = Model\Faq::next($filters['section']);

                return array(
                        'folder' => 'faq',
                        'file' => 'edit',
                        'action' => 'add',
                        'faq' => (object) array('section' => $filters['section'], 'order' => $next, 'cuantos' => $next),
                        'sections' => $sections
                );
                break;
            case 'edit':
                $faq = Model\Faq::get($id);

                $cuantos = Model\Faq::next($faq->section);
                $faq->cuantos = ($cuantos -1);

                return array(
                        'folder' => 'faq',
                        'file' => 'edit',
                        'action' => 'edit',
                        'faq' => $faq,
                        'sections' => $sections
                );
                break;
            case 'remove':
                Model\Faq::delete($id);
                break;
        }

        $faqs = Model\Faq::getAll($filters['section']);

        return array(
                'folder' => 'faq',
                'file' => 'list',
                'faqs' => $faqs,
                'sections' => $sections,
                'filters' => $filters
        );

    }

}

