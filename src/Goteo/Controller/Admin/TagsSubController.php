<?php
/**
 * Tags for blog
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
    Goteo\Application\Message,
	Goteo\Application\Config,
	Goteo\Library\Feed,
    Goteo\Model;

class TagsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Nuevo Tag',
      'move' => 'Moviendo a otro Nodo el proyecto',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe',
      'viewer' => 'Viendo logs',
      'edit' => 'Editando Tag',
      'translate' => 'Traduciendo Tag',
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


    static protected $label = 'Tags de blog';


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


    public function process ($action = 'list', $id = null) {

        $model = 'Goteo\Model\Blog\Post\Tag';
        $url = '/admin/tags';

        $errors = array();

        switch ($action) {
            case 'add':
                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => (object) array(),
                        'form' => array(
                            'action' => "$url/edit/",
                            'submit' => array(
                                'name' => 'update',
                                'label' => 'Añadir'
                            ),
                            'fields' => array (
                                'id' => array(
                                    'label' => '',
                                    'name' => 'id',
                                    'type' => 'hidden'

                                ),
                                'name' => array(
                                    'label' => 'Tag',
                                    'name' => 'name',
                                    'type' => 'text'
                                )
                            )
                    )
                );

                break;
            case 'edit':

                // gestionar post
                if ($this->isPost() && $this->hasPost('update')) {

                    $errors = array();

                    // instancia
                    $item = new $model(array(
                        'id' => $this->getPost('id'),
                        'name' => $this->getPost('name')
                    ));

                    if ($item->save($errors)) {

                        // tratar si han marcado pendiente de traducir
                        if ($this->getPost('pending') == 1 && !Model\Blog\Post\Tag::setPending($item->id, 'post')) {
                            Message::error('NO se ha marcado como pendiente de traducir!');
                        }

                        return $this->redirect($url);
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                } else {
                    $item = $model::get($id);
                }

                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => $item,
                        'form' => array(
                            'action' => "$url/edit/$id",
                            'submit' => array(
                                'name' => 'update',
                                'label' => Text::get('regular-save')
                            ),
                            'fields' => array (
                                'id' => array(
                                    'label' => '',
                                    'name' => 'id',
                                    'type' => 'hidden'

                                ),
                                'name' => array(
                                    'label' => 'Tag',
                                    'name' => 'name',
                                    'type' => 'text'
                                )
                            )
                    )
                );

                break;
            case 'remove':
                if ($model::delete($id)) {
                    return $this->redirect($url);
                }
                break;
        }

        return array(
                'folder' => 'base',
                'file' => 'list',
                'model' => 'tag',
                'addbutton' => 'Nuevo tag',
                'data' => $model::getList(1),
                'columns' => array(
                    'edit' => '',
                    'name' => 'Tag',
                    'used' => 'Entradas',
                    'translate' => '',
                    'remove' => ''
                ),
                'url' => "$url"
        );

    }

}

