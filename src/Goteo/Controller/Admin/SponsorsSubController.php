<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
	Goteo\Library\Feed,
	Goteo\Application\Message,
    Goteo\Model;

class SponsorsSubController extends AbstractSubController {

static protected $labels = array (
  'list' => 'Listando',
  'details' => 'Detalles del aporte',
  'update' => 'Cambiando el estado al aporte',
  'add' => 'Nuevo Patrocinador',
  'move' => 'Moviendo a otro Nodo el proyecto',
  'execute' => 'Ejecución del cargo',
  'cancel' => 'Cancelando aporte',
  'report' => 'Informe',
  'viewer' => 'Viendo logs',
  'edit' => 'Editando Patrocinador',
  'translate' => 'Traduciendo Destacado',
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
);


static protected $label = 'Apoyos institucionales';


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

        $node = $this->node;

        $model = 'Goteo\Model\Sponsor';
        $url = '/admin/sponsors';

        $errors = array();

        switch ($action) {
            case 'add':
                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => (object) array('order' => $model::next($node), 'node' => $node ),
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
                                'node' => array(
                                    'label' => '',
                                    'name' => 'node',
                                    'type' => 'hidden'

                                ),
                                'name' => array(
                                    'label' => 'Patrocinador',
                                    'name' => 'name',
                                    'type' => 'text'
                                ),
                                'url' => array(
                                    'label' => 'Enlace',
                                    'name' => 'url',
                                    'type' => 'text',
                                    'properties' => 'size=100'
                                ),
                                'image' => array(
                                    'label' => 'Logo',
                                    'name' => 'image',
                                    'type' => 'image'
                                ),
                                'order' => array(
                                    'label' => 'Posición',
                                    'name' => 'order',
                                    'type' => 'text'
                                )
                            )
                    )
                );

                break;
            case 'edit':

                // gestionar post
                if ($this->isPost()) {

                    // instancia
                    $item = new $model(array(
                        'id' => $this->getPost('id'),
                        'name' => $this->getPost('name'),
                        'node' => $this->getPost('node'),
                        'image' => $this->getPost('image'),
                        'url' => $this->getPost('url'),
                        'order' => $this->getPost('order')
                    ));

                    // tratar si quitan la imagen
                    if ($this->hasPost('image-' . md5($item->image) .  '-remove')) {
                        $image = Model\Image::get($item->image);
                        $image->remove($errors);
                        $item->image = null;
                        $removed = true;
                    }

                    // tratar la imagen y ponerla en la propiedad image
                    if(!empty($_FILES['image']['name'])) {
                        $item->image = $_FILES['image'];
                    }

                    if ($item->save($errors)) {
                        Message::info('Datos grabados correctamente');
                        return $this->redirect($url);
                    } else {
                        Message::error('No se han podido grabar los datos. ' . implode(', ', $errors));
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
                                'node' => array(
                                    'label' => '',
                                    'name' => 'node',
                                    'type' => 'hidden'

                                ),
                                'name' => array(
                                    'label' => 'Patrocinador',
                                    'name' => 'name',
                                    'type' => 'text'
                                ),
                                'url' => array(
                                    'label' => 'Enlace',
                                    'name' => 'url',
                                    'type' => 'text',
                                    'properties' => 'size=100'
                                ),
                                'image' => array(
                                    'label' => 'Logo',
                                    'name' => 'image',
                                    'type' => 'image'
                                ),
                                'order' => array(
                                    'label' => 'Posición',
                                    'name' => 'order',
                                    'type' => 'text'
                                )
                            )
                    )
                );

                break;
            case 'up':
                $model::up($id, $node);
                return $this->redirect($url);
                break;
            case 'down':
                $model::down($id, $node);
                return $this->redirect($url);
                break;
            case 'remove':
                if ($model::delete($id)) {
                    Message::info('Se ha eliminado el registro');
                    return $this->redirect($url);
                } else {
                    Message::info('No se ha podido eliminar el registro');
                }
                break;
        }

        return array(
                'folder' => 'base',
                'file' => 'list',
                'addbutton' => 'Nuevo patrocinador',
                'data' => $model::getAll($node),
                'columns' => array(
                    'edit' => '',
                    'name' => 'Patrocinador',
                    'url' => 'Enlace',
                    'image' => 'Imagen',
                    'order' => 'Posición',
                    'up' => '',
                    'down' => '',
                    'remove' => ''
                ),
                'url' => "$url"
        );

    }

}
