<?php
/**
 * Gestion de agrupaciones
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Message,
    Goteo\Model;

class OpenTagsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Nueva Agrupación',
      'move' => 'Reubicando el aporte',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe de proyecto',
      'viewer' => 'Viendo logs',
      'edit' => 'Editando Agrupación',
      'translate' => 'Traduciendo Agrupación',
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


    static protected $label = 'Agrupaciones';


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node allowed here
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    /**
     * This id is not opentag
     * @return [type] [description]
     */
    public static function getId() {
        return 'open_tags';
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


    public function process ($action = 'list', $id = null) {

        $model = 'Goteo\Model\OpenTag';
        $url = '/admin/open_tags';

        $errors = array();

        if ($this->isPost()) {

                if( ! ($post = $this->getPost('item')) ) {
                    error_log($el_item);
                    $post = null;
                }

                // objeto
                $open_tag = new Model\OpenTag(array(
                    'id' => $this->getPost('id'),
                    'name' => $this->getPost('name'),
                    'description' => $this->getPost('description'),
                    'order' => $this->getPost('order'),
                    'post' => $post
                ));

                if ($open_tag->save($errors)) {
                    Message::info('Datos guardados');

                    // tratar si han marcado pendiente de traducir
                    if ($this->getPost('pending') == 1 && !Model\OpenTag::setPending($open_tag->id, 'post')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                    return $this->redirect('/admin/open_tags');
                }

                else {
                Message::error(implode('<br />', $errors));

                // otros elementos disponibles
                $items = Model\Post::getAutocomplete();

                switch ($this->getPost('action')) {
                    case 'add':
                        return array(
                                'folder' => 'open_tags',
                                'file' => 'edit',
                                'action' => 'add',
                                'open_tag' => $open_tag,
                                'items' => $items,
                                'autocomplete' => true
                        );
                        break;
                    case 'edit':
                        return array(
                                'folder' => 'open_tags',
                                'file' => 'edit',
                                'action' => 'edit',
                                'story' => $open_tag,
                                'items' => $items,
                                'autocomplete' => true
                        );
                        break;
                }
            }
        }




        switch ($action) {

            case 'edit':

                $open_tag = Model\OpenTag::get($id, Config::get('lang'));
                    // elementos disponibles
                    $items = Model\Post::getAutocomplete();

                    return array(
                            'folder' => 'open_tags',
                            'file' => 'edit',
                            'action' => 'edit',
                            'open_tag' => $open_tag,
                            'items' => $items,
                            'autocomplete' => true
                    );

                break;

            case 'add':
                // siguiente orden
                $next = Model\OpenTag::next();
                // elementos disponibles
                $items = Model\Post::getAutocomplete();

                return array(
                        'folder' => 'open_tags',
                        'file' => 'edit',
                        'action' => 'add',
                        'open_tag' => (object) array('order' => $next),
                        'items' => $items,
                        'autocomplete' => true
                );
                break;

            case 'up':
                $model::up($id);
                break;
            case 'down':
                $model::down($id);
                break;
            case 'remove':
                if ($model::delete($id)) {
                    return $this->redirect($url);
                }
                break;
        }

        return array(
                'template' => 'admin/generic_list',
                'model' => 'open_tag',
                'addbutton' => 'Nueva agrupación',
                'data' => $model::getAll(),
                'columns' => array(
                    'edit' => '',
                    'name' => 'Agrupación',
                    'numProj' => 'Proyectos',
                    'order' => 'Prioridad',
                    'translate' => '',
                    'up' => '',
                    'down' => '',
                    'translate' => '',
                    'remove' => ''
                ),
                'url' => "$url"
        );

    }

}
